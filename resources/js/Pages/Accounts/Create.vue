<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Open new account
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="create">
                    <div>
                        <jet-label for="type" value="Choose account type"/>
                        <select class="input"
                                name="type"
                                id="type"
                                v-model="form.type"
                                required>
                            <option value="0">Debit account</option>
                            <option value="1">Saving account</option>
                        </select></div>
                    <jet-label for="type" value="Choose currency"/>
                    <div><select name="currency"
                                 id="currency"
                                 v-model="form.currency"
                                 required>
                        <option value="EUR">EUR</option>
                        <option v-for="currency in currencies"
                                :key="currency"
                                :value="currency.name">
                            {{ currency.name }}
                        </option>
                    </select></div>
                    <button class="btn btn-primary mt-6"
                            type="submit">Submit</button>
                </form>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import {useForm} from "@inertiajs/inertia-vue3";
import JetLabel from "@/Jetstream/Label";

export default {
    data() {
        return {
            currencies: []
        }
    },
    components: {
        AppLayout,
        JetLabel
    },
    setup() {
        const form = useForm({
            type: 0,
            currency: 'EUR',
        })
        return {form}
    },
    methods:
        {
            create() {
                this.form.post('/create')
            },
            getCurrencies() {
                axios.get('/api/getCurrencies/')
                    .then(res => {
                        res.data.shift()
                        this.currencies = res.data
                    })
                    .catch(error => console.log(error.message));
            }
        },
    beforeMount() {
        this.getCurrencies()
    }
}
</script>

<style scoped>
.input {
    margin-top: 10px;
    width: 300px;
}
</style>
