

<template>
    <div class="flex flex-col w-full md:w-3/12  gap-4 px-8 py-4 mx-auto bg-white rounded-lg shadow-md dark:bg-gray-800" style="cursor: auto;">
        <div class="flex items-center justify-between">
            <div class="flex justify-between">
                <span class="text-sm font-light text-gray-600 dark:text-gray-400">{{ started_at }}</span> 
            </div>
            <a class="px-3 py-1 text-sm font-bold text-gray-100 transition-colors duration-200 transform bg-gray-600 rounded cursor-pointer hover:bg-gray-500">{{ status }}</a>
        </div> 
        <div class="mt-2">
            <a class="text-2xl font-bold text-gray-700 dark:text-white hover:text-gray-600 dark:hover:text-gray-200 hover:underline">{{ title }}</a> 
            <p class="mt-2 text-gray-600 dark:text-gray-300">{{ description }}</p>
        </div> 
        <div class="flex justify-between mt-4">
            <div class="flex items-center">
                <img src="https://stackdiary.com/140x100.png" alt="Author Photo" class="hidden object-cover w-10 h-10 rounded-full sm:block"> 
                <a @click="showTasks" class="font-bold text-gray-700 cursor-pointer dark:text-gray-200">Show Tasks</a>
                <a-modal width="800px" v-model:visible="visible" title="Basic Modal" @ok="handleOk" @cancel="handleCancel">
                    <tasks>
                        <template #content>
                            <TaskItem v-for="(task, index) in tasks" 
                            :key="task.id" 
                            :started_at="task.started_at"
                            :deadline_at="task.deadline_at"
                            :status="task.status"
                            :title="task.title"
                            :sprint="task.sprint"
                            ></TaskItem>
                        </template>
                    </tasks>
                </a-modal>
            </div>
            <div class="flex flex-col">
                <span class="text-xs text-blue-400 font-bold dark:text-gray-400">deadline at: {{ deadline_at }}</span> 
                <span class="text-xs font-bold text-red-800 dark:text-gray-400">{{ dateStatus }}</span>
            </div>
        </div>
        <slot name="content"></slot>
    </div>
</template>

<script>
import Tasks from '@/Components/Tasks.vue';
import TaskItem from '@/Components/TaskItem.vue';

export default {
    props: [
        'started_at', 
        'deadline_at', 
        'description', 
        'status', 
        'title',
    ],

    emits: ['showTasks'],
    data(){
        return {
            dateStatus: '',
            visible: false,
            tasks: []
        }
    },

    methods: {
        showTasks(){
            // this.$emit('showTasks', {'id': this.$.vnode.key})
            this.visible = true;

            this.$nextTick(()=>{
                console.log(this.$.vnode.key)
                axios.get(route('project.show', { project: this.$.vnode.key })).then((response)=>{

                    // console.log(response.data)
                    this.tasks = response.data.projectTasks;
                })
            })
        }, 
        handleOk(){},
        handleCancel(){}
    },
    mounted(){
        // console.log(this.$.vnode.key);
        const started_at = moment(this.started_at, 'jYYYY/jM/jD HH:mm');
        const deadline_at = moment(this.deadline_at, 'jYYYY/jM/jD HH:mm')
        const current_date = moment().format('YYYY/M/D HH:mm')

        this.dateStatus = moment(deadline_at).isBefore(current_date) ? 'deadline is passed' : moment(deadline_at).fromNow();
    },
    components:{
        Tasks,
        TaskItem
    }
}


</script>