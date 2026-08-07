import './bootstrap';

import {
    getAllCitiesOfCountry,
    getCountries,
} from '@countrystatecity/countries-browser';
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/styles';

window.registrationForm = (savedCountry = '', savedCity = '') => ({
    loading: false,
    showPassword: false,
    showConfirmation: false,
    countries: [],
    cities: [],
    countryName: savedCountry,
    cityName: savedCity,
    countryOpen: false,
    cityOpen: false,
    countrySearch: '',
    citySearch: '',
    locationsLoading: true,
    citiesLoading: false,
    locationError: '',
    phoneInput: null,
    phoneDialCode: '',

    get selectedCountry() {
        return this.countries.find(country => country.name === this.countryName) || null;
    },

    get filteredCountries() {
        const search = this.countrySearch.trim().toLocaleLowerCase();
        if (!search) return this.countries;

        return this.countries.filter(country =>
            country.name.toLocaleLowerCase().includes(search)
            || country.iso2.toLocaleLowerCase().includes(search)
        );
    },

    get filteredCities() {
        const search = this.citySearch.trim().toLocaleLowerCase();
        const matches = search
            ? this.cities.filter(city => city.toLocaleLowerCase().includes(search))
            : this.cities;

        return matches.slice(0, 100);
    },

    openCountryDropdown() {
        if (this.locationsLoading || this.locationError) return;

        this.cityOpen = false;
        this.countryOpen = !this.countryOpen;
        this.countrySearch = '';
        if (this.countryOpen) this.$nextTick(() => this.$refs.countrySearch.focus());
    },

    openCityDropdown() {
        if (!this.countryName || this.citiesLoading || this.locationError) return;

        this.countryOpen = false;
        this.cityOpen = !this.cityOpen;
        this.citySearch = '';
        if (this.cityOpen) this.$nextTick(() => this.$refs.citySearch.focus());
    },

    async selectCountry(country) {
        this.countryName = country.name;
        this.countryOpen = false;
        await this.countryChanged(country.name);
    },

    selectCity(city) {
        this.cityName = city;
        this.cityOpen = false;
    },

    initPhoneInput() {
        this.phoneInput = intlTelInput(this.$refs.phoneNumber, {
            initialCountry: 'gb',
            nationalMode: false,
            formatAsYouType: true,
            autoPlaceholder: 'aggressive',
        });

        this.phoneInput.promise.then(() => {
            if (this.$refs.phoneNumber.value.trim()) {
                this.phoneInput.setNumber(this.$refs.phoneNumber.value);
            }

            this.phoneDialCode = this.phoneInput.getSelectedCountryData().dialCode;
            this.ensurePhoneDialCode();
        });

        this.$refs.phoneNumber.addEventListener('countrychange', () => {
            const previousDialCode = this.phoneDialCode;
            const currentValue = this.$refs.phoneNumber.value.trim();
            this.phoneDialCode = this.phoneInput.getSelectedCountryData().dialCode;

            if (!currentValue || currentValue === `+${previousDialCode}`) {
                this.$refs.phoneNumber.value = `+${this.phoneDialCode} `;
            }
        });
    },

    ensurePhoneDialCode() {
        if (!this.$refs.phoneNumber.value.trim()) {
            this.$refs.phoneNumber.value = `+${this.phoneDialCode} `;
        }
    },

    syncPhoneCountry(countryCode) {
        if (!this.phoneInput || !countryCode) return;

        const currentValue = this.$refs.phoneNumber.value.trim();
        const previousDialCode = this.phoneDialCode;
        this.phoneInput.setCountry(countryCode.toLocaleLowerCase());
        this.phoneDialCode = this.phoneInput.getSelectedCountryData().dialCode;

        if (!currentValue || currentValue === `+${previousDialCode}`) {
            this.$refs.phoneNumber.value = `+${this.phoneDialCode} `;
        }
    },

    async initLocations() {
        try {
            this.countries = (await getCountries())
                .sort((first, second) => first.name.localeCompare(second.name));

            const selectedCountry = this.countries.find(country =>
                country.name.toLocaleLowerCase() === this.countryName.toLocaleLowerCase()
                || country.iso2.toLocaleLowerCase() === this.countryName.toLocaleLowerCase()
            );

            if (selectedCountry) {
                this.countryName = selectedCountry.name;
                this.syncPhoneCountry(selectedCountry.iso2);
                await this.loadCities(selectedCountry.iso2, false);
            }
        } catch (error) {
            this.locationError = 'The location list could not be loaded. Please enter your location manually.';
        } finally {
            this.locationsLoading = false;
        }
    },

    async countryChanged(selectedCountryName = this.countryName) {
        this.countryName = selectedCountryName;
        this.cityName = '';
        const selectedCountry = this.countries.find(country => country.name === this.countryName);
        this.cities = [];

        if (!selectedCountry) return;

        this.syncPhoneCountry(selectedCountry.iso2);
        await this.loadCities(selectedCountry.iso2);
    },

    async loadCities(countryCode, clearCity = true) {
        this.citiesLoading = true;
        if (clearCity) this.cityName = '';

        try {
            const cityNames = (await getAllCitiesOfCountry(countryCode)).map(city => city.name);
            this.cities = [...new Set(cityNames)].sort((first, second) => first.localeCompare(second));
        } catch (error) {
            this.locationError = 'The city list could not be loaded. Please enter your location manually.';
        } finally {
            this.citiesLoading = false;
        }
    },
});
