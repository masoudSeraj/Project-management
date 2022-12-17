<template>
    <div class="flex" >
        <span  class="mb-2">
            <a-input :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" />
        </span>

        <span>
            <MinusOutlined @click="$emit('removeRow', key)" />
            <PlusOutlined @click="$emit('addRow', key)" />
            <!-- <i class="fas fa-minus-circle" @click="remove(k)" v-show="k || ( !k && inputs.length > 1)"></i>
            <i class="fas fa-plus-circle" @click="add(k)" v-show="k == inputs.length-1"></i> -->
        </span>
    </div>
</template>

<script>
import { PlusOutlined, MinusOutlined } from '@ant-design/icons-vue';

export default {
    props: ['inputs', 'modelValue', 'key'],

    components:{
        PlusOutlined, MinusOutlined
    }
}


</script>