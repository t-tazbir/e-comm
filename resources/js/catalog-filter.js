export function matchesCatalogFilters(product, selectedCategoryId, searchTerm) {
    const matchesCategory = selectedCategoryId === 'all' || product.categoryId === selectedCategoryId;
    const matchesSearch = product.searchText.includes(searchTerm);

    return matchesCategory && matchesSearch;
}
