<template>
  <div class="row">
    <div class="col-md-10 m-auto">
      <card :title="'Ordenes'">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              <th>
                Nro
              </th>
              <th>
                Cliente
              </th>
              <th>
                Estado
              </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="order in orders" :key="order.id">
              <th>
                {{order.number}}
              </th>
              <td>
                {{order.client}}
              </td>
              <td>
                <div v-if="order.state === 'rejected'" class="badge badge-danger">
                  RECHAZADA
                </div>
                <div v-if="order.state === 'pending'" class="badge badge-warning">
                  PENDIENTE
                </div>
                <div v-if="order.state === 'approved'" class="badge badge-success">
                  APROBADA
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
<!--        {{ orders }}-->
      </card>
    </div>
  </div>
</template>

<script>
  export default {
    data () {
      return {
        orders: []
      }
    },
    mounted () {
      this.getOrders()
    },
    methods: {
      async getOrders () {
        this.loading = true
        try {
          const resp = await this.$axios.get('api/get-orders')
          this.orders = resp.data
        } catch (e) {
          console.log(e)
        }
        this.loading = false
      }
    }
  }
</script>
