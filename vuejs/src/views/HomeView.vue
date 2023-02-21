<script setup>

import { computed, onMounted, onUnmounted, ref } from '@vue/runtime-core';    


let datas = ref([])

// async function fetchData()
// {
//   const headers = 
//   { 
//     "Content-Type": "application/json", 
//     "X-WP-Nonce": dposApi.nonce
//   };
//   await fetch(dposApi.root+"dpos/v1/products", { headers })
//   .then(response => response.json())
//   .then(data => ( datas.value = data));
    
// }

// function submitdata(){
//   let doctor = {
//       name: 'name'+Math.random(3,9),
//       email:"email"+Math.random(3,9)+"@gmail.com"
//   }
//   fetch('http://localhost/testwp/wp-json/crud-restapi/v1/posts',{
//     method:  'POST',
//     headers: {
//       'Content-Type': 'application/json'
//     },
//     body: JSON.stringify(doctor)
//   }).then(res=> fetchData())
  
// }

onMounted(()=>{
    //console.log(dposApi)
    // fetchData()
})





</script>
<template>
  <div>

    <h2> A minimalist pos plugin for woocommerce</h2>
    
  </div>
  </template>
