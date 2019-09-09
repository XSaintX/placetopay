<template>
  <div class="row">
    <div class="col-md-10 m-auto">
      <card :title="'Detalle de orden'">
        {{ order }}

      </card>
    </div>
  </div>
</template>

<script>
  export default {
    data () {
      return {
        order: [],
        ids: [1, 2, 3]
      }
    },
    mounted () {
      this.getOrder()
    },
    methods: {
      async getOrder () {
        this.loading = true
        try {
          const resp = await this.$axios.get(`api/get-order/${this.$route.params.order_number}`)
          this.order = resp.data
        } catch (e) {
          console.log(e)
        }
        this.loading = false
      }
    }
  }
</script>

<style lang="stylus">
</style>