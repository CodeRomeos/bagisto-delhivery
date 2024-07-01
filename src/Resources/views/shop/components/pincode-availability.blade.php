{{-- USAGE --}}
{{-- <div class="py-1 w-full lg:hidden max-w-md lg:mx-auto px-4">
    <x-shop::pincode-check.availabilityModal />
</div> --}}
<availability-modal></availability-modal>
@pushOnce('scripts')
    <script type="text/x-template" id="v-AvailabilityModal-template">

    <div class="rounded-sm">
      <div class="flex gap-x-2 text-[12px] items-start max-w-sm px-2 py-2 rounded-md my-12">
        <label for="pincode">

        <span class="icon-location inline-block text-[24px] cursor-pointer"></span></label>

          <div class="relative w-full">
            <input type="text" v-model="pincode" id="pincode" :class='{ "bg-green-100" : availability, "bg-gray-100 w-full px-2 py-2 border-0": true}' placeholder="Enter Pincode" v-on:enter.prevent.stop="checkAvailability" maxlength="6" />

            <div class="">
              <div v-if="availability" class='text-green-800 text-[12px]'>@{{message}} </div>
              <div v-if="!availability" class='text-red-500 text-[12px]' v-text="message"></div>
            </div>
          </div>
          <button v-on:click="checkAvailability" type='button' class="primary-button">
            <span v-if="loader" class="flex gap-x-2">wait. <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-2 animate-spin">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
              </svg>
              </span>
            <span v-else>Check</span>
          </button>
      </div>
  </div>
</script>

    <script type="module">
        app.component('availability-modal', {
            template: '#v-AvailabilityModal-template',
            data() {
                return {
                    pincode: null,
                    availability: null,
                    message: '',
                    loader: false
                };
            },
            methods: {
                checkForm: function(e) {
                    this.message = '';
                    if (!this.pincode) {
                        this.message = 'Pincode required.';
                        return false;
                    } else if (!this.validPincode(this.pincode)) {
                        this.message = 'Only numbers are allowed.'
                        return false;
                    }

                    return true;
                },

                validPincode: function(pincode) {
                    var re =
                        /^\d+$/;
                    return re.test(pincode);
                },

                async checkAvailability(e) {
                    this.loader = true
                    if (!this.checkForm()) {
                        this.loader = false
                        return false
                    }
                    // e.preventDefault();
                    // e.stopPropagation();
                    try {
                        const url =
                            `{{ route('shop.bagistodelhivery.estimateddelivery') }}`;
                        const params = {
                            delivery_postcode: this.pincode,
                            product_id: {{ $product->id }}
                        }
                        const response = await this.$axios.get(url, {
                            params: params
                        });

                        if (response.data && response.data.data) {
                            this.message = "Delivery in " + response.data.data?.available_courier_companies[0]
                                ?.estimated_delivery_days + " Days" + " - " + response.data.data
                                ?.available_courier_companies[0]
                                ?.etd
                            this.availability = 1
                        } else {
                            this.message = "Not deliverable"
                            this.availability = 0
                        }
                        this.loader = false
                    } catch (error) {
                        console.error("Error fetching availability:", error);
                        console.log(error.response.data.message)

                    }
                },
            },
        });
    </script>
    <style>
        .max-w-sm {
            max-width: 400px;
            border: thin solid #ddd;
        }

        .primary-button {
            padding: 6px 10px;
            border-radius: 6px
        }

        .gap-x-2 {
            gap: 10px
        }

        .my-12 {
            margin: 12px 0
        }

        .w-2 {
            width: 14px
        }
    </style>
@endPushOnce
