<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Your stock
            </h2>
        </template>
        <div v-for="stock in stocks" :key="stocks">
            <div class="mt-4 flex items-center justify-center px-4">
                <div class="max-w-4xl  bg-white w-full rounded-lg shadow-xl">
                    <div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Stock
                            </p>
                            <p>
                                {{ stock.name }}
                            </p>
                        </div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Price Bought
                            </p>
                            <p>
                                {{ formatCurrency(stock.price_bought, stock.currency) }}
                            </p>
                        </div>
                        <div v-if="stock.price_sold"
                             class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Price sold
                            </p>
                            <p>
                                {{ stock.price_sold }}
                            </p>
                        </div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Quantity bought
                            </p>
                            <p>
                                {{ stock.quantity }}
                            </p>
                        </div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Full price
                            </p>
                            <p>
                                {{ formatCurrency(stock.price_bought * stock.quantity, stock.currency) }}
                            </p>
                        </div>
                        <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p class="text-gray-600">
                                Status
                            </p>
                            <p>
                                {{ stock.status === 1 ? 'Active' : 'Sold' }}
                            </p>
                        </div>
                        <div class="flex justify-center hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                            <p>
                                <button class="btn btn-primary" @click="sell(stock.id)">Sell</button>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'

export default {
    components: {
        AppLayout
    },
    data() {
        return {
            stocks: []
        }
    },
    beforeMount() {
        this.getStocks()
    },
    methods: {
        getStocks() {
            axios.get('/api/getAllQuotes/')
                .then(res => {
                    this.stocks = res.data
                })
                .catch(e => console.log(e))
        },
        formatCurrency(money, currency) {
            return new Intl.NumberFormat('en-US', {style: 'currency', currency: currency}).format(money / 100)
        },
        sell(id) {
            axios.delete('/api/sellStock/' + id)
                .then(() => this.stocks.splice(id, 1))
                .catch(e => console.log(e.message))
        }
    }
}
</script>

