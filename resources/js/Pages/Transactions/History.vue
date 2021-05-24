<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transaction history
            </h2>
        </template>
        <div v-for="transaction in transactions" :key="transaction">
            {{ transaction }}<br>
            ------------------------------------------- <br>

        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";

export default {
    components: {
        AppLayout,
    },
    beforeMount() {
        this.getTransactions()
    },
    methods: {
        getTransactions() {
            axios.get('/api/history')
                .then(res => {
                    this.transactions = res.data
                })
                .catch(error => {
                    console.log(error.message)
                })
        }
    },
    data() {
        return {
            transactions: []
        }
    }
}
</script>

<style scoped>

</style>
