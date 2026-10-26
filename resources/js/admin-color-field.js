export function cardColorField(initialValue) {
    return {
        code: initialValue || '',
        picker: '#0452bc',
        init() { this.syncPicker(); },
        choose(value) {
            this.picker = value;
            this.code = value;
        },
        syncPicker() {
            let value = this.code.trim();
            if (/^#?[\da-f]{3}(?:[\da-f]{3})?$/i.test(value)) {
                value = `#${value.replace(/^#/, '')}`;
            } else if (!/^(rgb|hsl)\([^()]+\)$/i.test(value)) {
                return;
            }
            if (!CSS.supports('color', value)) return;

            const canvas = document.createElement('canvas');
            canvas.width = canvas.height = 1;
            const context = canvas.getContext('2d', { willReadFrequently: true });
            if (!context) return;
            context.fillStyle = value;
            context.fillRect(0, 0, 1, 1);
            const [r, g, b] = context.getImageData(0, 0, 1, 1).data;
            this.picker = `#${[r, g, b].map(channel => channel.toString(16).padStart(2, '0')).join('')}`;
        },
    };
}
