import { initRevisionSelection } from './revision-selection';

const read = (key, fallback = {}) => {
    try {
        return JSON.parse(localStorage.getItem(key)) ?? fallback;
    } catch {
        return fallback;
    }
};
const save = (key, value) => {
    try {
        localStorage.setItem(key, JSON.stringify(value));
    } catch {
        /* Private browsing can disable storage. */
    }
};
const speech = (text) => {
    if (!("speechSynthesis" in window)) return;
    speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = "en-GB";
    utterance.rate = 0.9;
    speechSynthesis.speak(utterance);
};
const youtube = (url) => {
    const match = (url || "").match(
        /(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([\w-]{11})/,
    );
    return match ? `https://www.youtube.com/embed/${match[1]}` : null;
};
const shared = {
    flags: {},
    get isFlagged() {
        return !!this.flags[this.current?.id];
    },
    toggleFlag() {
        if (!this.current) return;
        this.flags[this.current.id] = !this.flags[this.current.id];
        save("rivex.flags", this.flags);
    },
    speak() {
        if (this.current)
            speech(
                [
                    this.current.text_en,
                    ...this.current.choices.map((c) => c.text_en),
                ].join(". "),
            );
    },
    youtube,
    destroy() {
        if ("speechSynthesis" in window) speechSynthesis.cancel();
    },
};

window.v2Quiz = (config) => ({
    ...shared,
    questions: config.questions,
    languages: config.languages || [],
    language: config.language || "en",
    flaggedOnly: config.flagged,
    index: 0,
    answers: {},
    modal: null,
    word: "",
    flags: read("rivex.flags"),
    ad: null,
    adCountdown: 0,
    adShown: false,
    adTimer: null,
    definition: "",
    wordError: "",
    wordLoading: false,
    init() {
        if (this.flaggedOnly)
            this.questions = this.questions.filter((q) => this.flags[q.id]);
        if (config.questionId)
            this.index = Math.max(
                0,
                this.questions.findIndex(
                    (q) => String(q.id) === String(config.questionId),
                ),
            );
        if (!new URLSearchParams(location.search).has("language"))
            this.language = read("rivex.language", config.language || "en");
    },
    get current() {
        return this.questions[this.index] || null;
    },
    get answer() {
        return this.answers[this.current?.id]?.choiceId;
    },
    get answered() {
        return !!this.answers[this.current?.id];
    },
    get direction() {
        return (
            this.languages.find((l) => l.code === this.language)?.direction ||
            (["ku", "ckb", "ar", "fa"].includes(this.language) ? "rtl" : "ltr")
        );
    },
    translated(item, field) {
        return (
            item?.translations?.[this.language]?.[field] ||
            (["ku", "ckb"].includes(this.language)
                ? item?.[`${field}_ku`]
                : "") ||
            ""
        );
    },
    setLanguage(code) {
        this.language = code;
        save("rivex.language", code);
        this.modal = null;
    },
    async explainWord() {
        if (!this.current || !this.word.trim() || this.wordLoading) return;
        this.wordLoading = true;
        this.wordError = "";
        this.definition = "";
        try {
            const response = await fetch(
                `/questions/${this.current.id}/word-help`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        ).content,
                    },
                    body: JSON.stringify({
                        word: this.word,
                        language: this.language,
                    }),
                },
            );
            const data = await response.json();
            if (!response.ok) {
                this.wordError =
                    response.status === 401
                        ? "Sign in to use AI Word."
                        : response.status === 403
                          ? "Verify your email to use AI Word."
                          : data.message ||
                            "Word help is temporarily unavailable.";
                return;
            }
            this.definition = data.definition;
        } catch {
            this.wordError =
                "Word help is temporarily unavailable. Please try again.";
        } finally {
            this.wordLoading = false;
        }
    },
    select(choice) {
        if (this.answered) return;
        this.answers[this.current.id] = {
            choiceId: choice.id,
            isCorrect: !!choice.is_correct,
        };
        const progress = read("rivex.progress");
        progress[this.current.id] = {
            correct: !!choice.is_correct,
            topic: this.current.topic_id,
            at: new Date().toISOString(),
        };
        save("rivex.progress", progress);
    },
    previous() {
        if (this.index > 0) {
            this.index--;
            this.stopSpeech();
            window.scrollTo(0, 0);
        }
    },
    stopSpeech() {
        if ("speechSynthesis" in window) speechSynthesis.cancel();
    },
    next() {
        if (!this.current) return;
        const languageId = this.languages.find(
            (l) => l.code === this.language,
        )?.id;
        const eligibleAds = (config.ads || []).filter((ad) =>
            ad.languages?.length
                ? ad.languages.some((l) => String(l.id) === String(languageId))
                : ad.language_id === null ||
                  String(ad.language_id) === String(languageId),
        );
        if (
            !this.adShown &&
            Object.keys(this.answers).length >= 10 &&
            eligibleAds.length
        ) {
            this.ad =
                eligibleAds[Math.floor(Math.random() * eligibleAds.length)];
            this.adShown = true;
            this.adCountdown = 5;
            this.modal = "ad";
            this.adTimer = setInterval(() => {
                if (--this.adCountdown <= 0) clearInterval(this.adTimer);
            }, 1000);
            return;
        }
        this.stopSpeech();
        if (this.index < this.questions.length - 1) {
            this.index++;
            window.scrollTo(0, 0);
            return;
        }
        const reviews = this.questions
            .filter((q) => !this.answers[q.id]?.isCorrect)
            .map((q) => ({
                question: q.text_en,
                media: q.media_source,
                selected: q.choices.find(
                    (c) => c.id === this.answers[q.id]?.choiceId,
                ),
                correct: q.choices.find((c) => c.is_correct),
                explanation: q.explanation_en,
                choices: q.choices.map((c) => ({
                    ...c,
                    is_selected: c.id === this.answers[q.id]?.choiceId,
                })),
            }));
        sessionStorage.setItem(
            "practiceMistakeReview",
            JSON.stringify(reviews),
        );
        const correct = Object.values(this.answers).filter(
            (a) => a.isCorrect,
        ).length;
        location.href = `${config.result}?correct=${correct}&total=${this.questions.length}`;
    },
    get wordContext() {
        const word = this.word.trim().toLocaleLowerCase();
        if (!word || !this.current) return "";
        return (
            [
                this.current.text_en,
                ...this.current.choices.map((c) => c.text_en),
                this.current.explanation_en,
            ]
                .filter(Boolean)
                .filter((t) => t.toLocaleLowerCase().includes(word))
                .join("\n\n") ||
            "This word does not appear in the current question or its explanation."
        );
    },
    destroy() {
        this.stopSpeech();
        if (this.adTimer) clearInterval(this.adTimer);
    },
});

window.v2Exam = (config) => ({
    ...shared,
    questions: config.questions,
    index: 0,
    answers: {},
    flags: read("rivex.flags"),
    remaining: config.duration * 60,
    end: 0,
    timer: null,
    autoTimer: null,
    submitting: false,
    error: "",
    exitOpen: false,
    init() {
        if (!this.questions.length) return;
        this.end = Date.now() + config.duration * 60000;
        this.timer = setInterval(() => {
            this.remaining = Math.max(
                0,
                Math.ceil((this.end - Date.now()) / 1000),
            );
            if (this.remaining === 0 && !this.submitting && !this.error)
                this.finish();
        }, 1000);
        if (config.voice) this.$nextTick(() => this.speak());
    },
    get current() {
        return this.questions[this.index] || null;
    },
    get time() {
        return `${String(Math.floor(this.remaining / 60)).padStart(2, "0")}:${String(this.remaining % 60).padStart(2, "0")}`;
    },
    select(choice) {
        if (this.submitting) return;
        this.answers[this.current.id] = choice.id;
        if (this.autoTimer) clearTimeout(this.autoTimer);
        if (config.auto && this.index < this.questions.length - 1)
            this.autoTimer = setTimeout(() => this.next(), 350);
    },
    previous() {
        if (this.submitting) return;
        if (this.autoTimer) clearTimeout(this.autoTimer);
        if (this.index > 0) this.index--;
        this.changed();
    },
    next() {
        if (this.submitting) return;
        if (this.autoTimer) clearTimeout(this.autoTimer);
        if (this.index === this.questions.length - 1) {
            this.finish();
            return;
        }
        this.index++;
        this.changed();
    },
    changed() {
        window.scrollTo(0, 0);
        if ("speechSynthesis" in window) speechSynthesis.cancel();
        if (config.voice) this.$nextTick(() => this.speak());
    },
    async finish() {
        if (this.submitting || !this.questions.length) return;
        this.submitting = true;
        this.error = "";
        try {
            const response = await fetch(config.submit, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                },
                body: JSON.stringify({
                    question_ids: this.questions.map((q) => q.id),
                    answers: this.answers,
                    time_taken: config.duration * 60 - this.remaining,
                }),
            });
            if (!response.ok) throw new Error("submission");
            const data = await response.json();
            if (!data.redirect) throw new Error("redirect");
            clearInterval(this.timer);
            location.href = data.redirect;
        } catch {
            this.error =
                "Your answers are still here. We could not submit the test. Check your connection and select Finish to try again.";
            this.submitting = false;
        }
    },
    destroy() {
        clearInterval(this.timer);
        clearTimeout(this.autoTimer);
        if ("speechSynthesis" in window) speechSynthesis.cancel();
    },
});

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-share]").forEach((a) =>
        a.addEventListener("click", async (event) => {
            event.preventDefault();
            try {
                if (navigator.share)
                    await navigator.share({
                        title: "RIVEX.UK",
                        url: location.origin,
                    });
                else {
                    await navigator.clipboard.writeText(location.origin);
                    const label = a.querySelector(".tab__label");
                    if (label) {
                        label.textContent = "Copied";
                        setTimeout(() => (label.textContent = "Share"), 2500);
                    }
                }
            } catch {
                /* Share dismissal does not require feedback. */
            }
        }),
    );
    const progress = read("rivex.progress");
    document.querySelectorAll("[data-topic]").forEach((row) => {
        const ids = JSON.parse(row.dataset.questions);
        const answered = ids.filter((id) => progress[id]);
        const correct = answered.filter((id) => progress[id].correct);
        const percent = ids.length
            ? Math.round((correct.length / ids.length) * 100)
            : 0;
        row.querySelector("[data-answered]").textContent = answered.length;
        row.querySelector("[data-correct]").textContent = correct.length;
        row.querySelector("[data-pct]").textContent = `${percent}%`;
        row.querySelector("progress").value = percent;
    });
    const selection = document.querySelector("#revision-selection");
    if (selection) initRevisionSelection(selection, { read, save });
    document.querySelectorAll(".tabbar .tab").forEach((tab) => {
        if (tab.hasAttribute("data-share")) return;
        const pathname = new URL(tab.href, location.origin).pathname;
        const active =
            pathname === location.pathname ||
            (pathname === "/account" &&
                location.pathname.startsWith("/account")) ||
            (pathname === "/" &&
                !["/offers", "/account", "/login", "/register", "/more"].some(
                    (p) => location.pathname.startsWith(p),
                ));
        tab.classList.toggle("tab--active", active);
        if (active) tab.setAttribute("aria-current", "page");
        else tab.removeAttribute("aria-current");
    });
    const stats = document.querySelector("[data-progress-summary]");
    if (stats) {
        const answered = Object.values(progress);
        stats.querySelector("[data-total-answered]").textContent =
            answered.length;
        stats.querySelector("[data-total-correct]").textContent =
            answered.filter((a) => a.correct).length;
        stats.querySelector("[data-total-flags]").textContent = Object.values(
            read("rivex.flags"),
        ).filter(Boolean).length;
    }
    document.querySelectorAll("[data-instructor-save]").forEach((input) => {
        const saved = read("rivex.instructors");
        input.checked = !!saved[input.value];
        input.addEventListener("change", () => {
            const current = read("rivex.instructors");
            current[input.value] = input.checked;
            save("rivex.instructors", current);
        });
    });
});
