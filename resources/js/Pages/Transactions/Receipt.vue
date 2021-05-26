<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Receipt
            </h2>
        </template>
        <div v-for="receipt in receipts" :key="receipt" class="mt-4 flex items-center justify-center px-4 mb-10">
            <div class="max-w-4xl  bg-white w-full rounded-lg shadow-xl">
                <div>
                    <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                        <p class="text-gray-600">
                            Sender`s name
                        </p>
                        <p>
                            {{ sender.name }}
                        </p>
                    </div>
                    <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                        <p class="text-gray-600">
                            Senders`s account
                        </p>
                        <p>
                            {{ receipt.sender_account_number }}
                        </p>
                    </div>
                    <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                        <p class="text-gray-600">
                            Receiver`s name
                        </p>
                        <p>
                            {{ receiver.name }}
                        </p>
                    </div>
                    <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                        <p class="text-gray-600">
                            Receiver`s account number
                        </p>
                        <p>
                            {{ receipt.receiver_account_number }}
                        </p>
                    </div>
                    <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                        <p class="text-gray-600">
                            Amount
                        </p>
                        <p>
                            {{ formatCurrency(receipt.amount, receipt.currency) }}
                        </p>
                    </div>
                    <div v-if="receipt.tax"
                         class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                        <p class="text-gray-600">
                            Tax
                        </p>
                        <p>
                            {{ formatCurrency(receipt.tax, receipt.currency) }}
                        </p>
                    </div>
                    <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                        <p class="text-gray-600">
                            Purpose of payment
                        </p>
                        <p>
                            {{ receipt.purpose }}
                        </p>
                    </div>
                    <div class="md:grid md:grid-cols-2 hover:bg-gray-50 md:space-y-0 space-y-1 p-3 border-b">
                        <p class="text-gray-600">
                            Purpose of payment
                        </p>
                        <p>
                            {{ convertDate(receipt.created_at) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
    props: ['code'],
    components: {
        AppLayout
    },
    data() {
        return {
            index: null,
            receipts: {},
            sender: {},
            receiver: {},
        }
    },
    beforeMount() {
        if (this.$props.code) {
            this.getReceipt()
        } else {
            location.href = '/dashboard'
        }
    },
    methods: {
        getReceipt() {
            axios.get('/api/getReceipt/')
                .then(res => {
                    this.receipts = [res.data]
                    this.receiver = res.data['receiver']
                    this.sender = res.data['sender']
                })

        },
        formatCurrency(money, currency) {
            return new Intl.NumberFormat('en-US', {style: 'currency', currency: currency}).format(money / 100)
        },
        convertDate(date) {
            const DateTime = date.split('T')
            const time = DateTime[1].split('.')
            return DateTime[0] + ' ' + time[0];
        }
    }
}
</script>

<style scoped>

</style>
