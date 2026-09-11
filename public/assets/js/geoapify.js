
// document.addEventListener('DOMContentLoaded', () => {
//     const countryPickerInput = document.getElementById('country-picker-input');
//     const countryList = document.getElementById('country-list');
//     const shortNameInput = document.getElementById('short-name-input');
//     const selectedCountryFlag = document.getElementById('selected-country-flag');
//     const countryDropDown = document.getElementById('drop-men');
//     const popup = document.getElementById('popup');
//     const pickupInput = document.getElementById('pickup');
//     const dropInInput = document.getElementById('drop_in');
//     const pickupSuggestions = document.getElementById('pickup-suggestions');
//     const dropInSuggestions = document.getElementById('drop-in-suggestions');
//     let men = document.querySelector('.drop-men');
//     let selectedCountryCode = '';

//     // Fetch country data from the REST Countries API
//     fetch('https://restcountries.com/v3.1/all')
//         .then(response => response.json())
//         .then(countries => {
//             const countryData = countries.map(country => ({
//                 name: country.name.common,
//                 flag: country.flags.png,
//                 phone_code: country.idd?.root ? `${country.idd.root}${country.idd.suffixes ? country.idd.suffixes[0] : ''}` : '',
//                 symbol: country.currencies ? Object.keys(country.currencies)[0] : '',
//                 short_name: country.cca2.toLowerCase() // ISO alpha-2 code in lowercase
//             }));

//             countryPickerInput.addEventListener('focus', () => {
//                 countryList.style.display = 'block';
//             });

//             countryPickerInput.addEventListener('input', () => {
//                 const searchValue = countryPickerInput.value.toLowerCase();
//                 const filteredCountries = countryData.filter(country => country.name.toLowerCase().includes(searchValue));
//                 renderCountryList(filteredCountries);
//             });

//             document.addEventListener('click', (event) => {
//                 if (!event.target.closest('.country-picker-container') && !event.target.closest('.prof')) {
//                     countryList.style.display = 'none';
//                 }
//             });

//             renderCountryList(countryData);
//         })
//         .catch(error => {
//             console.error('Error fetching country data:', error);
//         });

//     function renderCountryList(countries) {
//         countryList.innerHTML = '';
//         countries.forEach(country => {
//             const countryElement = document.createElement('div');
//             countryElement.classList.add('country');
//             countryElement.innerHTML = `
//                 <span><img src="${country.flag}" alt="${country.name} flag" width="20" height="15"></span>
//                 <span>${country.name}</span>
//             `;
//             countryElement.addEventListener('click', () => {
//                 countryPickerInput.value = country.flag;
//                 countryPickerInput.value = country.name;
//                 shortNameInput.value = country.short_name;
//                 selectedCountryCode = country.short_name.toLowerCase(); // Convert to lowercase
//                 selectedCountryFlag.src = country.flag; // Update flag image
//                 selectedCountryFlag.style.display = 'block'; // Show selected flag

//                 countryList.style.display = 'none';
//                 countryDropDown.classList.remove('active'); // Hide the country picker menu
//             });
//             countryList.appendChild(countryElement);
//         });
//     }

//     function fetchSuggestions(input, suggestionsContainer) {
//         const query = input.value;

//         if (query.length < 3) {
//             suggestionsContainer.innerHTML = '';
//             suggestionsContainer.style.display = 'none';
//             return;
//         }

//         if (!selectedCountryCode) {
//             alert("Please select a country from the menu to proceed.");
//             // toastr.error('Please select a country from the menu to proceed.');
//             suggestionsContainer.innerHTML = '';
//             suggestionsContainer.style.display = 'none';
//             men.classList.toggle('active');
//             return;
//         }

//         fetch(`https://api.geoapify.com/v1/geocode/autocomplete?text=${query}&apiKey=85058d4a99b941dc9dd85c05eaab666d&filter=countrycode:${selectedCountryCode}`)
//             .then(response => response.json())
//             .then(data => {
//                 suggestionsContainer.innerHTML = '';
//                 if (data.features.length > 0) {
//                     data.features.forEach(feature => {
//                         const p = document.createElement('p');
//                         p.textContent = feature.properties.formatted;
//                         p.addEventListener('click', () => {
//                             input.value = feature.properties.formatted;
//                             suggestionsContainer.innerHTML = '';
//                             suggestionsContainer.style.display = 'none';
//                         });
//                         suggestionsContainer.appendChild(p);
//                     });
//                     suggestionsContainer.style.display = 'block';
//                 } else {
//                     suggestionsContainer.style.display = 'none';
//                 }
//             })
//             .catch(error => {
//                 console.error('Error fetching suggestions:', error);
//                 suggestionsContainer.style.display = 'none';
//             });
//     }

//     pickupInput.addEventListener('focus', () => {
//         popup.classList.add('active');
//     });

//     dropInInput.addEventListener('focus', () => {
//         popup.classList.add('active');
//     });

//     pickupInput.addEventListener('input', () => fetchSuggestions(pickupInput, pickupSuggestions));
//     dropInInput.addEventListener('input', () => fetchSuggestions(dropInInput, dropInSuggestions));
// });


document.addEventListener('DOMContentLoaded', () => {
    const countryPickerInput = document.getElementById('country-picker-input');
    const countryList = document.getElementById('country-list');
    const shortNameInput = document.getElementById('short-name-input');
    const selectedCountryFlag = document.getElementById('selected-country-flag');
    const countryDropDown = document.getElementById('drop-men');
    const popup = document.getElementById('popup');
    const pickupInput = document.getElementById('pickup');
    const dropInInput = document.getElementById('drop_in');
    const pickupSuggestions = document.getElementById('pickup-suggestions');
    const dropInSuggestions = document.getElementById('drop-in-suggestions');
    let men = document.querySelector('.drop-men');
    let selectedCountryCode = '';

    // Function to load the selected country from session storage
    function loadSelectedCountry() {
        const storedCountry = sessionStorage.getItem('selectedCountry');
        if (storedCountry) {
            const country = JSON.parse(storedCountry);
            selectedCountryCode = country.short_name.toLowerCase();
            selectedCountryFlag.src = country.flag;
            selectedCountryFlag.style.display = 'block';
            countryPickerInput.value = country.name;
            shortNameInput.value = country.short_name;
        }
    }

    // Function to save the selected country in session storage
    function saveSelectedCountry(country) {
        sessionStorage.setItem('selectedCountry', JSON.stringify(country));
    }

    // Fetch country data from the REST Countries API
    fetch('https://restcountries.com/v3.1/all')
        .then(response => response.json())
        .then(countries => {
            const countryData = countries.map(country => ({
                name: country.name.common,
                flag: country.flags.png,
                phone_code: country.idd?.root ? `${country.idd.root}${country.idd.suffixes ? country.idd.suffixes[0] : ''}` : '',
                symbol: country.currencies ? Object.keys(country.currencies)[0] : '',
                short_name: country.cca2.toLowerCase() // ISO alpha-2 code in lowercase
            }));

            countryPickerInput.addEventListener('focus', () => {
                countryList.style.display = 'block';
            });

            countryPickerInput.addEventListener('input', () => {
                const searchValue = countryPickerInput.value.toLowerCase();
                const filteredCountries = countryData.filter(country => country.name.toLowerCase().includes(searchValue));
                renderCountryList(filteredCountries);
            });

            document.addEventListener('click', (event) => {
                if (!event.target.closest('.country-picker-container') && !event.target.closest('.prof')) {
                    countryList.style.display = 'none';
                }
            });

            renderCountryList(countryData);
            loadSelectedCountry(); // Load the selected country on page load
        })
        .catch(error => {
            console.error('Error fetching country data:', error);
        });

    function renderCountryList(countries) {
        countryList.innerHTML = '';
        countries.forEach(country => {
            const countryElement = document.createElement('div');
            countryElement.classList.add('country');
            countryElement.innerHTML = `
                <span><img src="${country.flag}" alt="${country.name} flag" width="20" height="15"></span>
                <span>${country.name}</span>
            `;
            countryElement.addEventListener('click', () => {
                countryPickerInput.value = country.name;
                shortNameInput.value = country.short_name;
                selectedCountryCode = country.short_name.toLowerCase(); // Convert to lowercase
                selectedCountryFlag.src = country.flag; // Update flag image
                selectedCountryFlag.style.display = 'block'; // Show selected flag

                countryList.style.display = 'none';
                countryDropDown.classList.remove('active'); // Hide the country picker menu
                saveSelectedCountry(country); // Save selected country in session storage
            });
            countryList.appendChild(countryElement);
        });
    }

    function fetchSuggestions(input, suggestionsContainer) {
        const query = input.value;

        if (query.length < 3) {
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
            return;
        }

        if (!selectedCountryCode) {
            alert("Please select a country from the menu to proceed.");
            suggestionsContainer.innerHTML = '';
            suggestionsContainer.style.display = 'none';
            men.classList.toggle('active');
            return;
        }

        fetch(`https://api.geoapify.com/v1/geocode/autocomplete?text=${query}&apiKey=85058d4a99b941dc9dd85c05eaab666d&filter=countrycode:${selectedCountryCode}`)
            .then(response => response.json())
            .then(data => {
                suggestionsContainer.innerHTML = '';
                if (data.features.length > 0) {
                    data.features.forEach(feature => {
                        const p = document.createElement('p');
                        p.textContent = feature.properties.formatted;
                        p.addEventListener('click', () => {
                            input.value = feature.properties.formatted;
                            suggestionsContainer.innerHTML = '';
                            suggestionsContainer.style.display = 'none';
                        });
                        suggestionsContainer.appendChild(p);
                    });
                    suggestionsContainer.style.display = 'block';
                } else {
                    suggestionsContainer.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                suggestionsContainer.style.display = 'none';
            });
    }

    pickupInput.addEventListener('focus', () => {
        popup.classList.add('active');
    });

    dropInInput.addEventListener('focus', () => {
        popup.classList.add('active');
    });

    pickupInput.addEventListener('input', () => fetchSuggestions(pickupInput, pickupSuggestions));
    dropInInput.addEventListener('input', () => fetchSuggestions(dropInInput, dropInSuggestions));
});
