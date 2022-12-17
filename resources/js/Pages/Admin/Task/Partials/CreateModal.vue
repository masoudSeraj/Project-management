
<template>
    <a-button @click="visible = true" type="primary" shape="round" style="display: flex; align-items: center;">
        <template #icon>
            <slot name="icon"></slot>
        </template>
        {{ title }}
    </a-button>

    <a-modal v-model:visible="visible" title="Create a new collection" ok-text="Create" cancel-text="Cancel"
        @ok="submitTask">
        <section class="flex gap-1">
            <div class="w-1/2">
                <a-form layout="vertical" name="form_in_modal">
                    <a-form-item name="task name" label="Title" :rules="[
                        { required: true, message: 'Please input the title of collection!' },
                    ]">
                        <a-input v-model:value="taskName" />
                    </a-form-item>

                    <a-form-item name="task name" label="description" :rules="[
                        { required: true, message: 'Please input the title of collection!' },
                    ]">
                            <a-textarea v-model:value="taskDescription" placeholder="Description" :rows="4" />
                    </a-form-item>

                    <a-select
                        ref="select"
                        v-model:value="status"
                        style="width: 120px"
                        @change="selectStatus"
                        >
                        <a-select-option value="Active">Active</a-select-option>
                        <a-select-option value="Suspended">Suspended</a-select-option>
                        <a-select-option value="Completed">Completed</a-select-option>
                    </a-select>

                    <div dir="rtl" class="text-left">
                        <label for="">
                            Deadline date
                        </label>
                        <datetime v-model="date"
                         type="datetime"
                         format="YYYY-MM-DD HH:mm:ss"
                         display-format="dd jDD jMMMM jYYYY  HH:mm:ss"
                         ></datetime>
                    </div>
                    

                </a-form>
            </div>

            <div class="w-1/2">
                <div class=" mr-1 mb-2"><span class="text-red-500">*</span> Tasks:</div>
                <dynamic-input v-for="(input, key) in inputs" :key="key" v-model="input.title" :inputs="inputs" @removeRow="removeRow" @addRow="addRow"></dynamic-input>
            </div>
        </section>

    </a-modal>
</template>

<script>
import { nextTick } from '@vue/runtime-core';
import { notification } from 'ant-design-vue';
import DynamicInput from './DynamicInput.vue';
import VuePersianDatetimePicker from 'vue3-persian-datetime-picker'
import { Inertia } from '@inertiajs/inertia'

export default {
    props: ['title'],
    emits: ['taskCreated'],
    data() {
        return {
            visible: false,
            taskName: '',
            taskDescription: '',
            inputs: [
                {
                    title: ''
                }
            ],
            date: '',
            status: ''
        }
    },
    methods: {
         submitTask() {
            axios.post(route('task.store'), { 
                'title': this.taskName,
                'description': this.taskDescription,
                'task'  : this.inputs,
                'date': this.date,
                'status': this.status
             })
            .then((response) => {
                // console.log(response.data)
                this.$emit('taskCreated');
                this.$nextTick(()=>{
                    notification.success({
                        message: response.data.message,
                    });
                    Inertia.visit(route('task.index'), {
                    preserveScroll: true
                });
                })
            
                
            })
            .catch((error)=>{
                notification.error({
                    message: error.response.data.message,
                });
            })
        },
        handleOk(e) {
            console.log(e);
            this.visible = false;
        },
        removeRow(index){
            this.inputs.splice(index, 1);
        },
        addRow(index){
            this.inputs.push({ title: '' });
        },
        selectStatus(value) {
            this.status = value;
        }
    },
    components: {
        DynamicInput, 
        'datetime': VuePersianDatetimePicker
    }
}
</script>
