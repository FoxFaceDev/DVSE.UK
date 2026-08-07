import './bootstrap';

import Alpine from 'alpinejs';
import {
    getCitiesOfState,
    getCountries,
} from '@countrystatecity/countries-browser';
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/styles';
import cityDataFiles from './location-city-files.json';

const phoneInputInstances = new WeakMap();

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
        const phoneElement = this.$refs.phoneNumber;
        const phoneInput = intlTelInput(phoneElement, {
            initialCountry: 'gb',
            formatAsYouType: true,
            separateDialCode: true,
            loadUtils: () => import('intl-tel-input/utils'),
        });
        phoneInputInstances.set(phoneElement, phoneInput);

        phoneInput.promise.then(() => {
            if (phoneElement.value.trim()) {
                phoneInput.setNumber(phoneElement.value);
            }

            this.updatePhoneNumber();
        });

        phoneElement.addEventListener('countrychange', () => this.updatePhoneNumber());
        phoneElement.addEventListener('input', () => this.updatePhoneNumber());
    },

    updatePhoneNumber() {
        const phoneInput = phoneInputInstances.get(this.$refs.phoneNumber);
        if (!phoneInput) return;

        this.$refs.phoneNumberValue.value = phoneInput.getNumber() || this.$refs.phoneNumber.value.trim();
    },

    prepareRegistrationSubmission() {
        this.updatePhoneNumber();
        this.loading = true;
    },

    syncPhoneCountry(countryCode) {
        const phoneInput = phoneInputInstances.get(this.$refs.phoneNumber);
        if (!phoneInput || !countryCode) return;

        phoneInput.setSelectedCountry(countryCode.toLocaleLowerCase());
        this.updatePhoneNumber();
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
            const stateCodes = cityDataFiles[countryCode.toLocaleUpperCase()] || [];
            const cityGroups = await Promise.all(
                stateCodes.map(stateCode => getCitiesOfState(countryCode, stateCode))
            );
            const cityNames = cityGroups.flat().map(city => city.name);
            this.cities = [...new Set(cityNames)].sort((first, second) => first.localeCompare(second));
        } catch (error) {
            this.locationError = 'The city list could not be loaded. Please enter your location manually.';
        } finally {
            this.citiesLoading = false;
        }
    },
});

window.Alpine = Alpine;
Alpine.start();
