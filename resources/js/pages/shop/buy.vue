<template>
  <div class="row">
    <div class="col-md-10 m-auto">
      <card :title="'Finalizar compra'">
        <form-wizard
          stepSize="sm"
          title="Finalice su compra"
          subtitle="Confirme sus datos para procesar el pago"
          nextButtonText="Continuar"
          finishButtonText="Procesar"
          backButtonText="Volver"
          @on-complete="submit">
          <tab-content title="Seleccionar productos">
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                    <tr>
                      <th>SKU</th>
                      <th>Producto</th>
                      <th>Precio</th>
                      <th>Cantidad</th>
                      <th>Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="product in calcProducts" :key="product.id">
                      <th>
                        <span v-text="product.sku"></span>
                      </th>
                      <td>
                        <span v-text="product.name"></span>
                      </td>
                      <td>
                        <span v-text="product.price"></span>
                      </td>
                      <td>
                        <input class="form-control qty-input"
                               v-model="product.quantity">
                      </td>
                      <td>
                        <span v-text="product.subtotal"></span>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </tab-content>
          <tab-content title="Ingrese domicilio">
            <div class="form-group">
              <label for="name">Ingrese nombre</label>
              <input id="name" type="text" v-model="form.name"
                     placeholder="Dirección" class="form-control">
            </div>
            <div class="form-group">
              <label for="last_name">Ingrese apellido</label>
              <input id="last_name" type="text" v-model="form.last_name"
                     placeholder="Apellido" class="form-control">
            </div>
            <div class="form-group">
              <label for="address">Ingrese dirección</label>
              <input id="address" type="text"
                     v-model="form.address"
                     placeholder="Dirección" class="form-control">
            </div>
            <div class="form-group">
              <label for="phone">Ingrese teléfono</label>
              <input type="text" id="phone" v-model="form.phone"
                     placeholder="Teléfono" class="form-control">
            </div>
          </tab-content>
          <tab-content title="Forma de pago">
            <label for="">PlaceToPay</label>
            <input type="checkbox" v-model="form.payment" value="placetopay" class="input-sm">
          </tab-content>
          <tab-content title="Pago">
            Será redirigido a la página de PlaceToPay para continuar con la compra segura
          </tab-content>
        </form-wizard>
      </card>
    </div>
  </div>
</template>

<script>
  import { FormWizard, TabContent } from 'vue-form-wizard'
  import 'vue-form-wizard/dist/vue-form-wizard.min.css'
  import Vue from 'vue'
  import { Form, HasError, AlertError } from 'vform'
  import Swal from 'sweetalert2'

  Vue.component(HasError.name, HasError)
  Vue.component(AlertError.name, AlertError)

  export default {
    components: {
      FormWizard,
      TabContent
    },
    data () {
      return {
        products: [
          {
            id: 1,
            sku: '343KBG',
            name: 'Pelota',
            price: 15,
            quantity: 0,
            subtotal: 0
          },
          {
            id: 2,
            sku: '4435LLS',
            name: 'Camiseta',
            price: 32,
            quantity: 0,
            subtotal: 0
          },
        ],
        form: new Form({
          name: '',
          last_name: '',
          address: '',
          phone: '',
          products: [],
          payment: 'placetopay'
        })
      }
    },
    computed: {
      calcProducts () {
        return this.products.map((item) => {
          item.subtotal = item.quantity * item.price
          return item
        })
      },
    },
    methods: {
      async submit () {
        this.form.products = this.calcProducts
        const resp = await this.form.post('api/submit-order')
        if (resp.data.error) {
          Swal.fire({
            type: 'error',
            title: resp.data.msg
          })
        } else {
          window.location.href = resp.data.redirect_url
        }
      }
    },
    events: {}
  }
</script>

<style scoped>
  .qty-input {
    max-width: 60px;
  }
</style>
