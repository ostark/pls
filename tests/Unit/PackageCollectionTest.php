<?php


it('it sorts collection by numeric field ASC', function ($field) {
    $collection = dummyCollection(5)->shuffle();
    $collection = $collection->sortByField($field, false);

    $plucked1 = $collection->pluck($field)->toArray();

    $plucked2 = $collection->pluck($field)->toArray();
    asort($plucked2);

    expect(implode('.', $plucked1))->toEqual(implode('.', $plucked2));
})->with([
    'downloads', 'favers', 'dependents'
]);

it('it sorts collection by field DESC', function ($field) {
    $collection = dummyCollection(5)->shuffle();
    $collection = $collection->sortByField($field, true);

    $plucked1 = $collection->pluck($field)->toArray();

    $plucked2 = $collection->pluck($field)->toArray();
    arsort($plucked2);

    expect(implode('.', $plucked1))->toEqual(implode('.', $plucked2));

})->with([
    'downloads', 'testLibrary'
]);
