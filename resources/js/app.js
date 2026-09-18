/**
 * AutoBazaar frontend entry point.
 *
 * Deliberately does NOT import ./bootstrap — that file pulls in Bootstrap 5
 * and axios for the legacy site. The new frontend is Tailwind + Alpine only.
 */
import Alpine from 'alpinejs';

import compareStore from './stores/compare';
import cartStore from './stores/cart';

import addToCart from './components/add-to-cart';
import emiCalculator from './components/emi-calculator';
import operatingCost from './components/operating-cost';
import locationPicker from './components/location-picker';
import filterRail from './components/filter-rail';
import financeOptions from './components/finance-options';
import earningsCalculator from './components/earnings-calculator';
import leadForm from './components/lead-form';
import detailTabs from './components/detail-tabs';
import vehicleGallery from './components/vehicle-gallery';

import initReveal from './lib/reveal';

// Shared across the cards, the tray and the WhatsApp button — see the store.
Alpine.store('compare', compareStore);
Alpine.store('cart', cartStore);

Alpine.data('addToCart', addToCart);
Alpine.data('emiCalculator', emiCalculator);
Alpine.data('operatingCost', operatingCost);
Alpine.data('locationPicker', locationPicker);
Alpine.data('filterRail', filterRail);
Alpine.data('financeOptions', financeOptions);
Alpine.data('earningsCalculator', earningsCalculator);
Alpine.data('leadForm', leadForm);
Alpine.data('detailTabs', detailTabs);
Alpine.data('vehicleGallery', vehicleGallery);

window.Alpine = Alpine;
Alpine.start();

// Entrance animations. Runs after Alpine so x-cloak content is measurable.
document.addEventListener('DOMContentLoaded', initReveal);
