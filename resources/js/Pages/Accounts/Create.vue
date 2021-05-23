<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Open new account
            </h2>
        </template>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    components:{
        AppLayout
    },
    setup() {
        const form = useForm({
            type: null,
            currency: null,
        })
        return {form}
    },
}
</script>

<style scoped>

</style>
