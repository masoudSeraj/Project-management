<template>
    <a-button @click="edit(id)" type="primary" shape="circle">
        <template #icon>
            <edit-outlined />
        </template>
    </a-button>

    <a-modal v-model:visible="visible" title="Create a new collection" ok-text="Create" cancel-text="Cancel"
        @ok="updateProject(id)">
        <section class="flex gap-1">
            <div class="w-1/2">
                <a-form layout="vertical" name="form_in_modal">
                    <a-form-item name="project name" label="Title" :rules="[
                        { required: true, message: 'Please input the title of collection!' },
                    ]">
                        <a-input v-model:value="projectName" />
                    </a-form-item>

                    <a-form-item name="project name" label="description" :rules="[
                        { required: true, message: 'Please input the title of collection!' },
                    ]">
                            <a-textarea v-model:value="projectDescription" placeholder="Description" :rows="4" />
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
                <dynamic-input  v-for="(task, key) in tasks" :key="key" v-model="task.title" :tasks="tasks" @removeRow="removeRow" @addRow="addRow"></dynamic-input>
            </div>
        </section>

    </a-modal>
    
</template>

<script>
import { EditOutlined } from '@ant-design/icons-vue';
import { notification } from 'ant-design-vue';
import DynamicInput from './DynamicInput.vue';
import VuePersianDatetimePicker from 'vue3-persian-datetime-picker'

export default {
    props: ["id", "name"],
    emits: ['changeRoleName'],
    data() {
        return {
            visible: false,
            projectName: '',
            projectDescription: '',
            tasks: [
                {
                    title: '',
                    id: ''
                }
            ],
            date: '',
            status: ''
        }
    },

    methods: {

        edit(projectId) {
            this.visible = true;
            axios
                .get(route("project.show", { project: projectId }))
                .then((response) => {
                    const data = response.data;
                    this.tasks = data.tasks;
                    this.projectDescription = data.projectDescription;
                    this.projectName = data.projectName;
                    this.date = data.date;
                    this.status = data.status;

                });
        },
        updateProject(projectId){
            axios.put(route('project.update', {project: projectId}), {
                tasks: this.tasks,
                projectDescription: this.projectDescription,
                projectName: this.projectName,
                date: this.date,
                status: this.status
            }).then(response => {
                notification.success({
                        message: response.data.message,
                    });
            }).catch(error => {
                notification.message({
                        message: error.response.data.message,
                    });
            })
        },


        handleOk(e) {
            console.log(e);
            this.visible = false;
        },
        removeRow(index){
            this.tasks.splice(index, 1);
        },
        addRow(index){
            this.tasks.push({ title: '' });
        },
        selectStatus(value) {
            this.status = value;
        }
    },
    components: {
        EditOutlined, 
        DynamicInput,
        'datetime': VuePersianDatetimePicker,
    },
};
</script>