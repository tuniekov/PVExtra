<template>
  <UniTreePanel :treetabs="pvextra"/>
</template>

<script setup>
  import { PVTables, UniTreePanel } from 'pvtables/dist/pvtables'
  import { ref } from 'vue'
  const pvextra = ref({
    gsProductTree2:{
      type:'tree',
      title: 'Product Tree 2',
      table: 'gsProductTree2',
    },
    gsProductTree3:{
      type:'tree',
      title: 'Product Tree 3',
      table: 'gsProductTree3',
    },
  });
</script>
<style>
 #pvextra{
  display: flex;
  width: auto;
  height: 100vh;
 }
</style>