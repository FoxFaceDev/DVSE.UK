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
    locationsLoading: true,
    citiesLoading: false,
    locationError: '',
    phoneInput: null,
    phoneDialCode: '',

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

    async countryChanged() {
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
