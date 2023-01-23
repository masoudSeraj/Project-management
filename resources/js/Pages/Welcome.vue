<template>
    <Head title="Welcome" />

    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            <Link v-if="$page.props.auth.user && $page.props.auth.user.roles.filter((role)=>{ return role.name == 'admin' }).length > 0" :href="route('dashboard')" class="text-sm text-gray-700 underline">
                Dashboard
            </Link>

            <template v-else-if="!$page.props.auth.user">
                <Link :href="route('login')" class="text-sm text-gray-700 underline">
                    Log in
                </Link>

                <Link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-gray-700 underline">
                    Register
                </Link>
            </template>

            <Link v-else method="post" :href="route('logout')" class="text-sm text-gray-700 underline">
                logout
            </Link>
        </div>
        <!-- <template >
            <div v-text="console.log($page.props)">

            </div>
        </template> -->
        <template v-if="!$page.props.auth.user">
            لطفا برای نمایش وضعیت پروژه ها در سایت ثبت نام کنید.
        </template>
        
        <template v-else-if="projects.data.length && $page.props.auth != undefined">
            <div class="flex gap-4 container mx-auto">
                <Project v-for="(project, index) in projects.data" :key="project.id" 
                    :started_at="project.started_at"
                    :deadline_at="project.deadline_at"
                    :title="project.title"
                    :status="project.status"
                    :description="project.description"
                    >
                </Project>
            </div>

        </template>
        <template v-else>
            در حال حاضر در هیچ پروژه ای مشارکت ندارید.
        </template>


    </div>
</template>

<script>
import { Head, Link, Inertia } from '@inertiajs/inertia-vue3';
import Project from '@/Components/Project.vue';

export default {
    props: {
        canLogin: Boolean,
        canRegister: Boolean,
        laravelVersion: String,
        phpVersion: String,
        projects: Object
    },
    data(){
        return {}
    },
    methods:{},
    mounted(){
        // console.log(this.$page.props.auth.user.roles.filter((role)=>{ return role.name == 'admin' }).length > 0)
        console.log(this.$page.props.auth)
    },
    components: {
        Project, Link, Head
    }
}
</script>

<style scoped>
    .bg-gray-100 {
        background-color: #f7fafc;
        background-color: rgba(247, 250, 252, var(--tw-bg-opacity));
    }

    .border-gray-200 {
        border-color: #edf2f7;
        border-color: rgba(237, 242, 247, var(--tw-border-opacity));
    }

    .text-gray-400 {
        color: #cbd5e0;
        color: rgba(203, 213, 224, var(--tw-text-opacity));
    }

    .text-gray-500 {
        color: #a0aec0;
        color: rgba(160, 174, 192, var(--tw-text-opacity));
    }

    .text-gray-600 {
        color: #718096;
        color: rgba(113, 128, 150, var(--tw-text-opacity));
    }

    .text-gray-700 {
        color: #4a5568;
        color: rgba(74, 85, 104, var(--tw-text-opacity));
    }

    .text-gray-900 {
        color: #1a202c;
        color: rgba(26, 32, 44, var(--tw-text-opacity));
    }

    @media (prefers-color-scheme: dark) {
        .dark\:bg-gray-800 {
            background-color: #2d3748;
            background-color: rgba(45, 55, 72, var(--tw-bg-opacity));
        }

        .dark\:bg-gray-900 {
            background-color: #1a202c;
            background-color: rgba(26, 32, 44, var(--tw-bg-opacity));
        }

        .dark\:border-gray-700 {
            border-color: #4a5568;
            border-color: rgba(74, 85, 104, var(--tw-border-opacity));
        }

        .dark\:text-white {
            color: #fff;
            color: rgba(255, 255, 255, var(--tw-text-opacity));
        }

        .dark\:text-gray-400 {
            color: #cbd5e0;
            color: rgba(203, 213, 224, var(--tw-text-opacity));
        }
    }
</style>
