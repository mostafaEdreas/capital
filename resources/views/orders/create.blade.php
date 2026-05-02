@extends('layouts.dashboard')

@section('title', 'Create Order')

@section('content')
<div class="mx-auto max-w-7xl" x-data="posSystem({{ $bottles }}, {{ $ingredients }})">
    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Unable to create the order.</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">New Order</h1>
            <p class="text-sm text-slate-500">Build bottle items, validate capacity, and submit in one flow.</p>
        </div>
        <button
            type="button"
            @click="addBottleCard()"
            class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
        >
            Add Bottle Item
        </button>
    </div>

    <form action="{{ route('orders.store') }}" method="POST" @submit.prevent="submitForm">
        @csrf
        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">
        <div class="grid gap-6 lg:grid-cols-12">
            <div class="space-y-4 lg:col-span-8">
                <template x-if="cards.length === 0">
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                        No items yet. Add your first bottle item.
                    </div>
                </template>

                <template x-for="(card, cardIndex) in cards" :key="card.uuid">
                    <section class="rounded-xl border bg-white shadow-sm" :class="card.isOverloaded ? 'border-red-300' : 'border-slate-200'">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <div class="flex items-center justify-between">
                                <h2 class="text-sm font-semibold text-slate-800" x-text="`Item #${cardIndex + 1}`"></h2>
                                <button
                                    type="button"
                                    @click="removeBottleCard(cardIndex)"
                                    class="text-xs font-medium text-red-600 transition hover:text-red-700"
                                >
                                    Remove Item
                                </button>
                            </div>
                        </div>

                        <div class="space-y-5 px-5 py-5">
                            <div class="grid gap-4 md:grid-cols-12">
                                <div class="md:col-span-7">
                                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-slate-500">Bottle</label>
                                    <select
                                        x-model="card.bottle_id"
                                        @change="updateBottleData(cardIndex)"
                                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"
                                    >
                                        <option value="">Select bottle</option>
                                        <template x-for="bottle in availableBottles" :key="bottle.id">
                                            <option :value="bottle.id" x-text="`${bottle.name} (${bottle.size}g)`"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-slate-500">Quantity</label>
                                    <div class="flex overflow-hidden rounded-lg border border-slate-300">
                                        <button type="button" class="px-3 text-slate-600 hover:bg-slate-100" @click="decreaseBottleQuantity(cardIndex)">-</button>
                                        <input type="number" x-model.number="card.bottle_quantity" class="w-full border-0 text-center text-sm font-semibold focus:ring-0" readonly>
                                        <button type="button" class="px-3 text-slate-600 hover:bg-slate-100" @click="increaseBottleQuantity(cardIndex)">+</button>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-slate-500">Capacity</label>
                                    <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-center text-sm font-semibold text-slate-700">
                                        <span x-text="card.bottle_size || 0"></span>g
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                Bottle subtotal:
                                <span class="font-semibold text-slate-900">$<span x-text="(card.bottle_price * card.bottle_quantity).toFixed(2)"></span></span>
                                <span class="mx-2 text-slate-300">|</span>
                                Ingredients subtotal:
                                <span class="font-semibold text-slate-900">$<span x-text="card.ingredients_total_for_item.toFixed(2)"></span></span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Ingredients</p>
                                    <button
                                        type="button"
                                        class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700 transition hover:bg-emerald-100"
                                        @click="addIngredient(cardIndex)"
                                    >
                                        Add Ingredient
                                    </button>
                                </div>

                                <template x-if="card.ingredients.length === 0">
                                    <div class="rounded-lg border border-dashed border-slate-300 px-4 py-3 text-sm text-slate-500">
                                        Add at least one ingredient.
                                    </div>
                                </template>

                                <template x-for="(ingredient, ingredientIndex) in card.ingredients" :key="ingredient.uuid">
                                    <div class="grid gap-3 rounded-lg border border-slate-200 bg-white p-3 md:grid-cols-12">
                                        <div class="md:col-span-4">
                                            <select
                                                x-model="ingredient.ingredient_id"
                                                @change="updateIngredientTypes(cardIndex, ingredientIndex)"
                                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"
                                            >
                                                <option value="">Ingredient</option>
                                                <template x-for="item in availableIngredients" :key="item.id">
                                                    <option :value="item.id" x-text="item.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div class="md:col-span-4">
                                            <select
                                                x-model="ingredient.ingredient_type_id"
                                                @change="syncTypePrice(cardIndex, ingredientIndex)"
                                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"
                                            >
                                                <option value="">Type</option>
                                                <template x-for="type in ingredient.available_types" :key="type.id">
                                                    <option :value="type.id" x-text="`${type.name} ($${parseFloat(type.price).toFixed(2)}/${type.for_gram}g)`"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div class="md:col-span-3">
                                            <div class="relative">
                                                <input
                                                    type="number"
                                                    min="1"
                                                    x-model.number="ingredient.sold_quantity_grams"
                                                    @input="calculateCardTotals(cardIndex)"
                                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"
                                                    placeholder="Grams"
                                                >
                                                <span class="absolute right-3 top-2.5 text-xs text-slate-400">g</span>
                                            </div>
                                        </div>
                                        <div class="md:col-span-1">
                                            <button
                                                type="button"
                                                @click="removeIngredient(cardIndex, ingredientIndex)"
                                                class="w-full rounded-lg border border-red-200 px-2 py-2 text-xs font-medium text-red-600 transition hover:bg-red-50"
                                            >
                                                X
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="flex items-center justify-between rounded-lg border px-4 py-3" :class="card.isOverloaded ? 'border-red-200 bg-red-50 text-red-700' : 'border-slate-200 bg-slate-50 text-slate-700'">
                                <p class="text-sm font-medium">
                                    Ingredients: <span x-text="card.total_grams"></span>g /
                                    <span x-text="card.bottle_size"></span>g
                                </p>
                                <p class="text-sm font-semibold">
                                    Item Total: $<span x-text="card.card_total_price.toFixed(2)"></span>
                                </p>
                            </div>
                            <p class="text-xs text-slate-500">
                                Formula: (Bottle price x Quantity) + (Ingredients per bottle x Quantity)
                            </p>
                        </div>
                    </section>
                </template>
            </div>

            <aside class="lg:col-span-4">
                <div class="sticky top-4 space-y-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-slate-900">Order Summary</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Bottle items</span>
                            <span class="font-semibold text-slate-900" x-text="cards.length"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Total bottles</span>
                            <span class="font-semibold text-slate-900" x-text="total_bottles_quantity"></span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                            <span class="font-medium text-slate-700">Grand Total</span>
                            <span class="text-lg font-semibold text-emerald-600">$<span x-text="total_price.toFixed(2)"></span></span>
                        </div>
                    </div>

                    <div class="rounded-lg border px-3 py-2 text-sm" :class="isSubmitDisabled ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'">
                        <template x-if="isSubmitDisabled">
                            <p>Complete all bottle and ingredient fields and resolve capacity warnings to continue.</p>
                        </template>
                        <template x-if="!isSubmitDisabled">
                            <p>Order is valid and ready to submit.</p>
                        </template>
                    </div>

                    <button
                        type="submit"
                        :disabled="isSubmitDisabled"
                        class="w-full rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                    >
                        Submit Order
                    </button>
                </div>
            </aside>
        </div>
    </form>
</div>

<script>
function posSystem(bottles, ingredients) {
    return {
        availableBottles: bottles,
        availableIngredients: ingredients,
        cards: [],
        total_price: 0,
        total_bottles_quantity: 0,
        isSubmitDisabled: true,

        init() {
            this.addBottleCard();
        },

        uniqueId() {
            return `${Date.now()}-${Math.floor(Math.random() * 100000)}`;
        },

        addBottleCard() {
            this.cards.push({
                uuid: this.uniqueId(),
                bottle_id: '',
                bottle_size: 0,
                bottle_price: 0,
                bottle_quantity: 1,
                ingredients: [],
                total_grams: 0,
                ingredients_total_per_bottle: 0,
                ingredients_total_for_item: 0,
                card_total_price: 0,
                isOverloaded: false
            });
            this.calculateGlobalTotals();
        },

        removeBottleCard(cardIndex) {
            this.cards.splice(cardIndex, 1);
            this.calculateGlobalTotals();
        },

        increaseBottleQuantity(cardIndex) {
            this.cards[cardIndex].bottle_quantity++;
            this.calculateCardTotals(cardIndex);
        },

        decreaseBottleQuantity(cardIndex) {
            if (this.cards[cardIndex].bottle_quantity > 1) {
                this.cards[cardIndex].bottle_quantity--;
                this.calculateCardTotals(cardIndex);
            }
        },

        updateBottleData(cardIndex) {
            const bottle = this.availableBottles.find((item) => item.id == this.cards[cardIndex].bottle_id);
            if (bottle) {
                this.cards[cardIndex].bottle_size = parseFloat(bottle.size);
                this.cards[cardIndex].bottle_price = parseFloat(bottle.price);
            } else {
                this.cards[cardIndex].bottle_size = 0;
                this.cards[cardIndex].bottle_price = 0;
            }
            this.calculateCardTotals(cardIndex);
        },

        addIngredient(cardIndex) {
            this.cards[cardIndex].ingredients.push({
                uuid: this.uniqueId(),
                ingredient_id: '',
                ingredient_type_id: '',
                available_types: [],
                sold_quantity_grams: 1,
                unit_price: 0,
                for_gram: 1,
                calculated_price: 0
            });
            this.calculateCardTotals(cardIndex);
        },

        removeIngredient(cardIndex, ingredientIndex) {
            this.cards[cardIndex].ingredients.splice(ingredientIndex, 1);
            this.calculateCardTotals(cardIndex);
        },

        updateIngredientTypes(cardIndex, ingredientIndex) {
            const ingredient = this.cards[cardIndex].ingredients[ingredientIndex];
            const ingredientModel = this.availableIngredients.find((item) => item.id == ingredient.ingredient_id);
            ingredient.available_types = ingredientModel ? ingredientModel.types : [];
            ingredient.ingredient_type_id = '';
            ingredient.unit_price = 0;
            ingredient.for_gram = 1;
            ingredient.calculated_price = 0;
            this.calculateCardTotals(cardIndex);
        },

        syncTypePrice(cardIndex, ingredientIndex) {
            const ingredient = this.cards[cardIndex].ingredients[ingredientIndex];
            const selectedType = ingredient.available_types.find((type) => type.id == ingredient.ingredient_type_id);
            if (selectedType) {
                ingredient.unit_price = parseFloat(selectedType.price);
                ingredient.for_gram = parseFloat(selectedType.for_gram);
            } else {
                ingredient.unit_price = 0;
                ingredient.for_gram = 1;
            }
            this.calculateCardTotals(cardIndex);
        },

        calculateCardTotals(cardIndex) {
            const card = this.cards[cardIndex];
            let ingredientGrams = 0;
            let ingredientPriceTotal = 0;

            card.ingredients.forEach((ingredient) => {
                const grams = Number(ingredient.sold_quantity_grams) || 0;
                ingredient.sold_quantity_grams = grams;
                ingredient.calculated_price = (grams / (ingredient.for_gram || 1)) * ingredient.unit_price;
                ingredientGrams += grams;
                ingredientPriceTotal += ingredient.calculated_price;
            });

            card.total_grams = ingredientGrams;
            card.isOverloaded = ingredientGrams > card.bottle_size && card.bottle_size > 0;
            card.ingredients_total_per_bottle = ingredientPriceTotal;
            card.ingredients_total_for_item = ingredientPriceTotal * card.bottle_quantity;
            card.card_total_price = (card.bottle_price * card.bottle_quantity) + card.ingredients_total_for_item;
            this.calculateGlobalTotals();
        },

        calculateGlobalTotals() {
            this.total_price = this.cards.reduce((sum, card) => sum + card.card_total_price, 0);
            this.total_bottles_quantity = this.cards.reduce((sum, card) => sum + (Number(card.bottle_quantity) || 0), 0);

            this.isSubmitDisabled = this.cards.length === 0 || this.cards.some((card) => {
                if (!card.bottle_id || card.bottle_quantity < 1 || card.ingredients.length === 0 || card.isOverloaded) {
                    return true;
                }

                return card.ingredients.some((ingredient) =>
                    !ingredient.ingredient_id ||
                    !ingredient.ingredient_type_id ||
                    (Number(ingredient.sold_quantity_grams) || 0) < 1
                );
            });
        },

        buildPayload() {
            return this.cards.map((card) => ({
                bottle_id: Number(card.bottle_id),
                bottle_quantity: Number(card.bottle_quantity),
                ingredients: card.ingredients.map((ingredient) => ({
                    ingredient_id: Number(ingredient.ingredient_id),
                    ingredient_type_id: Number(ingredient.ingredient_type_id),
                    sold_quantity_grams: Number(ingredient.sold_quantity_grams),
                })),
            }));
        },

        submitForm(event) {
            if (this.isSubmitDisabled) {
                return;
            }

            const form = event.target;
            form.querySelectorAll('input[data-dynamic-item]').forEach((input) => input.remove());

            const items = this.buildPayload();

            items.forEach((item, itemIndex) => {
                this.appendHiddenInput(form, `items[${itemIndex}][bottle_id]`, item.bottle_id);
                this.appendHiddenInput(form, `items[${itemIndex}][bottle_quantity]`, item.bottle_quantity);

                item.ingredients.forEach((ingredient, ingredientIndex) => {
                    this.appendHiddenInput(form, `items[${itemIndex}][ingredients][${ingredientIndex}][ingredient_id]`, ingredient.ingredient_id);
                    this.appendHiddenInput(form, `items[${itemIndex}][ingredients][${ingredientIndex}][ingredient_type_id]`, ingredient.ingredient_type_id);
                    this.appendHiddenInput(form, `items[${itemIndex}][ingredients][${ingredientIndex}][sold_quantity_grams]`, ingredient.sold_quantity_grams);
                });
            });

            form.submit();
        },

        appendHiddenInput(form, name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            input.dataset.dynamicItem = 'true';
            form.appendChild(input);
        }
    };
}
</script>
@endsection