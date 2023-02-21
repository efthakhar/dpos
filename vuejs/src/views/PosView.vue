<script setup>
import { computed, onMounted, onUnmounted, ref } from "@vue/runtime-core";

let search_product_string = ref("");
let search_products = ref([]);
let order_details = ref([]);
let products = ref([]);
let query_categories = ref([]);
let query_tags = ref([]);
let page = ref(1);
let total_page = ref(1);
let loader = ref(false);
let success_message = ref(false)

let cart_products = ref({});
let total_cart_amount = ref(0);
let total_paying_amount = ref(0);

let customers = ref([]);
let customer_query_string = ref("");
let selected_customer = ref("");

function show_success_message(){
  success_message.value = true
  setTimeout(()=>{
    success_message.value= false
  },3000)
}

async function get_products() {
  loader.value = true;

  let cats = encodeURIComponent(JSON.stringify(query_categories.value));
  let tags = encodeURIComponent(JSON.stringify(query_tags.value));

  const headers = {
    "Content-Type": "application/json",
    "X-WP-Nonce": dposApi.nonce,
  };
  await fetch(
    `${dposApi.root}dpos/v1/products?categories=${cats}&tags=${tags}&page=${page.value}`,
    { headers }
  )
    .then((response) => response.json())
    .then((data) => {
      products.value = data.data.products;
      total_page.value = data.data.max_page;
      console.log(data);
    });

  loader.value = false;
}

function changePage(p) {
  products.value = [];
  page.value = p;
  get_products();
}

async function get_customers_by_name() {
  const headers = {
    "Content-Type": "application/json",
    "X-WP-Nonce": dposApi.nonce,
  };
  await fetch(
    `${dposApi.root}dpos/v1/customers-by-name?query_string=${customer_query_string.value}`,
    { headers }
  )
    .then((response) => response.json())
    .then((data) => (customers.value = data.data || []));
}
async function get_query_products() {
  const headers = {
    "Content-Type": "application/json",
    "X-WP-Nonce": dposApi.nonce,
  };
  await fetch(
    `${dposApi.root}dpos/v1/products-by-search?query_string=${search_product_string.value}`,
    { headers }
  )
    .then((response) => response.json())
    .then((data) => (search_products.value = data.data));
}

function clear_search() {
  search_products.value = [];
  search_product_string.value = "";
}

function clear_customers() {
  customers.value = [];
}

function increaseTotalCartAmount(quantity, price) {
  total_cart_amount.value += quantity * Math.floor(price);
}
function add_to_cart(id, e) {
  if (cart_products.value[id]) {
    cart_products.value[id].quantity += 1;
    increaseTotalCartAmount(1, cart_products.value[id].price);
  } else {
    let product = JSON.parse(
      e.target.closest(".product").getAttribute("data-product")
    );
    product.quantity = 1;
    cart_products.value[product.id] = product;
    increaseTotalCartAmount(1, product.price);
  }
}

function add_to_cart_from_search_list(id, e) {
  if (cart_products.value[id]) {
    cart_products.value[id].quantity += 1;
    increaseTotalCartAmount(1, cart_products.value[id].price);
  } else {
    let product = JSON.parse(
      e.target.closest(".sp_item").getAttribute("data-product")
    );
    product.quantity = 1;
    cart_products.value[product.id] = product;
    increaseTotalCartAmount(1, product.price);
  }
}

function deleteCartItem(id) {
  total_cart_amount.value -=
    cart_products.value[id].quantity *
    Math.floor(cart_products.value[id].price);
  delete cart_products.value[id];
}

function pay() {
  fetch(`${dposApi.root}dpos/v1/orders`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      'cart_products': cart_products.value,
      'total_cart_amount': total_cart_amount.value,
      'total_paying_amount': total_paying_amount.value,
    }),
  }).then((res) => {
    if(res.status==200){
      show_success_message()
      cart_products.value = {}
      total_cart_amount.value = 0
      total_paying_amount.value = 0
    }
  });
}

onMounted(() => {
  get_products();
});
</script>

<template>
  <div class="pos-system">
    <div class="pos-container">
      <div class="pos-product-search-section">
        <div @mouseleave="clear_search" class="product_search">
          <input
            type="text"
            class="product_query-input"
            placeholder="Search Products..."
            v-model="search_product_string"
            @keyup="get_query_products"
          />
          <div class="product_search_list" v-if="search_products.length > 0">
            <li
              v-for="sp in search_products"
              :key="sp.id"
              class="sp_item"
              @click="add_to_cart_from_search_list(sp.id, $event)"
              :data-product="JSON.stringify(sp)"
            >
              <span>{{ sp.name }}</span>
              <span class="price">{{ sp.price }}$</span>
              <div class="img_container"><img :src="sp.image" alt="" /></div>
            </li>
          </div>
        </div>

        <div class="loader-container" v-if="loader == true">
          <div class="lds-hourglass loader"></div>
        </div>
        <div class="products-with-pagination">
          <div class="products">
            <div
              class="product"
              @click="add_to_cart(product.id, $event)"
              v-for="product in products"
              :key="product.id"
              :data-product="JSON.stringify(product)"
            >
              <img :src="product.image" alt="" />
              <div class="product_details">
                <span>{{ product.name.substring(0, 10) }}</span>
                <span>{{ product.price }}</span>
              </div>
            </div>
          </div>

          <nav aria-label="Page navigation example" v-if="loader != true">
            <ul class="pagination">
              <li class="page-item" v-if="page - 1 > 0">
                <a class="page-link" @click="changePage(page - 1)">{{
                  page - 1
                }}</a>
              </li>
              <li class="page-item">
                <a class="page-link active" @click="changePage(page)">{{
                  page
                }}</a>
              </li>
              <li class="page-item" v-if="page + 1 < total_page + 1">
                <a class="page-link" @click="changePage(page + 1)">{{
                  page + 1
                }}</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>

      <div class="pos-addtocart-section">
        <div class="cart_products">
          <li class="cart_product" v-for="cp in cart_products" :key="cp.id">
            <button
              class="del_product_from_cart"
              @click="deleteCartItem(cp.id)"
            >
              del
            </button>
            <div class="cart_product_img_container">
              <img :src="cp.image" alt="" />
            </div>
            <span>{{ cp.name.substring(0, 15) }}</span>
            <input type="number" class="cp_q" v-model="cp.quantity" />
            <span>{{ cp.price }}</span>
            <span>{{ cp.price * cp.quantity }}</span>
          </li>
        </div>
        <div class="cart_order">
          <!-- <div class="customers" @mouseleave="clear_customers">
            <label for="">add customer</label> 
            <input type="text" v-model="customer_query_string" 
            placeholder="search customer......" @keyup="get_customers_by_name"
            >
             
            <div v-if="customers.length>0" class="customers_list"> 
              <li v-for="customer in customers" :key="customer.id"
                  @click="selected_customer=customer.id"> 
                 {{ customer.email }}
              </li>
            </div>
  
          </div> -->
          <div class="alert alert-success alert-dismissible fade show" v-if="success_message==true">
               Order created successfully.
          </div>
          <div>total: {{ total_cart_amount }}</div>
          <input type="number" v-model="total_paying_amount" />
          <button class="btn btn-danger" @click="pay">pay</button>

        </div>
      </div>
    </div>
  </div>
</template>

<style>
.customer_list {
  position: absolute;
  top: 20px;
  width: 100%;
}

.cart_order {
  position: absolute;
  width: 100%;
  bottom: 0;
  left: 0;
  border: 2px solid red;
  padding: 20px;
  background-color: aliceblue;
  z-index: 1000;
}
.cart_products {
  padding: 20px 10px;
  overflow: scroll;
  width: 100%;
  height: 90%;
}
.cart_product {
  font-size: 13px;
  display: flex;
  /* justify-content: space-between; */
  align-items: center;
}
.cart_product > *:not(:last-child) {
  margin-right: 10px;
}
.cart_product_img_container {
  width: 50px;
  height: 50px;
}
.cart_product_img_container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.cp_q {
  margin-left: auto;
  width: 50px !important;
  border: none;
}
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
}

/* #wpadminbar{display: none;} */
.loader-container {
  position: relative;
  width: 100%;
  margin-top: 100px;
}

.lds-hourglass {
  display: inline-block;
  /* position: relative; */
  width: 80px;
  height: 80px;
}
.lds-hourglass:after {
  content: " ";
  display: block;
  border-radius: 50%;
  width: 0;
  height: 0;
  margin: 8px;
  box-sizing: border-box;
  border: 32px solid #e41717;
  border-color: #ff0000 transparent #1310ce transparent;
  animation: lds-hourglass 1.2s infinite;
}
@keyframes lds-hourglass {
  0% {
    transform: rotate(0);
    animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
  }
  50% {
    transform: rotate(900deg);
    animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
  }
  100% {
    transform: rotate(1800deg);
  }
}
.loader {
  position: absolute !important;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.pos-container {
  padding: 20px 10px;
  width: 100%;
  display: flex;
  align-items: flex-start;
}

.pos-product-search-section {
  width: 46%;
  flex: 0 0 46%;
  padding: 10px;
}
.product_search {
  position: relative;
}
.product_search_list {
  position: absolute;
  top: 30px;
  left: 0;
  width: 100%;
  display: block;
  z-index: 1000;
  background-color: white !important;
}
.product_query-input {
  display: block;
  width: 100% !important;
}
.product_search_list {
  margin: 3px 0 0 0;
  padding: 0 5px;
  border: 1px solid red;
}
.product_search_list li {
  list-style: none;
  display: flex;
  align-items: flex-start;
  padding: 2px;
  margin: 0;
  border-bottom: 1px solid rgba(145, 143, 143, 0.281);
  cursor: pointer;
}

.product_search_list li .price {
  margin-left: auto;
  margin-right: 5px;
}
.product_search_list li .img_container {
  width: 30px;
  height: 25px;
}
.product_search_list li .img_container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.products {
  display: flex;
  width: 100%;
  /* justify-content: space-between; */
  /* align-items: center; */
  flex-wrap: wrap;
}

.product {
  /* flex: 0 0 30%; */
  width: 130px;
  height: 100px;
  margin: 6px;
  position: relative;
}
.product:hover {
  transform: scale(1.01);
  cursor: pointer;
}
.product img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.product .product_details {
  padding: 2px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.26);
  color: white;
  font-size: 14px !important;
}
.page-item {
  cursor: pointer;
}

/* =====Cart section==== */

.pos-addtocart-section {
  width: 45%;
  flex: 0 0 45%;
  position: fixed;
  height: 95vh;
  /* background-color: yellow; */
  right: 0;
  bottom: 0;
  border: 1px solid green;
}
</style>
