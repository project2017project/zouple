<!DOCTYPE html>

<html>

<head>

    <title>Razorpay</title>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

</head>

<body>

<script>

var options = {

    key: "{{ config('razorpay.key') }}",

    amount: "{{ $order['amount'] }}",

    currency: "INR",

    name: "Zouple",

    description: "Order Payment",

    order_id: "{{ $order['id'] }}",

    prefill: {

        name: "{{ $user->name }}",

        email: "{{ $user->email }}"

    },

    handler: function (response) {

        fetch("{{ route('razorpay.success') }}",{

            method:'POST',

            headers:{

                'Content-Type':'application/json',

                'X-CSRF-TOKEN':'{{ csrf_token() }}'

            },

            body:JSON.stringify({

                razorpay_payment_id:response.razorpay_payment_id,

                razorpay_order_id:response.razorpay_order_id,

                razorpay_signature:response.razorpay_signature

            })

        })
        .then(res=>res.json())
        .then(function(data){

            window.location="/yourOrder";

        });

    }

};

var rzp = new Razorpay(options);

rzp.open();

</script>

</body>

</html>
