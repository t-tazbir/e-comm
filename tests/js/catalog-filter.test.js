import assert from 'node:assert/strict';
import test from 'node:test';
import { matchesCatalogFilters } from '../../resources/js/catalog-filter.js';

const demoProducts = [
    { name: 'Daily Glow Serum', categoryId: 'beauty-health', searchText: 'daily glow serum beauty & health ritual' },
    { name: 'Wildflower Honey', categoryId: 'food', searchText: 'wildflower honey food sunshine' },
    { name: 'Roasted Cashews', categoryId: 'food', searchText: 'roasted cashews food crunch' },
    { name: 'Wireless Buds', categoryId: 'electronics', searchText: 'wireless buds electronics sound' },
    { name: 'Heritage Face Watch', categoryId: 'watch', searchText: 'heritage face watch classic' },
    { name: 'Cabin Trolley', categoryId: 'suitcase-trolley', searchText: 'cabin trolley suitcase travel' },
    { name: 'Amber Eau de Parfum', categoryId: 'perfume', searchText: 'amber eau de parfum perfume' },
];

const flashProducts = demoProducts.filter((product) => product.name !== 'Roasted Cashews');

const filterProducts = (products, categoryId, searchTerm = '') => products.filter((product) => (
    matchesCatalogFilters(product, categoryId, searchTerm.toLocaleLowerCase())
));

test('category selection filters flash sales and all products by the same category ID', () => {
    const categoryId = 'food';

    assert.deepEqual(filterProducts(flashProducts, categoryId).map((product) => product.name), ['Wildflower Honey']);
    assert.deepEqual(filterProducts(demoProducts, categoryId).map((product) => product.name), ['Wildflower Honey', 'Roasted Cashews']);
});

test('All Categories restores the full flash sale and product catalogs', () => {
    assert.equal(filterProducts(flashProducts, 'all').length, flashProducts.length);
    assert.equal(filterProducts(demoProducts, 'all').length, demoProducts.length);
});

test('category and text search conditions combine instead of overriding each other', () => {
    assert.deepEqual(filterProducts(demoProducts, 'food', 'honey').map((product) => product.name), ['Wildflower Honey']);
    assert.deepEqual(filterProducts(demoProducts, 'perfume', 'honey'), []);
});
