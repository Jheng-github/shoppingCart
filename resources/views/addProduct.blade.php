<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>
<!--
  This example requires some changes to your config:
  
  ```
  // tailwind.config.js
  module.exports = {
    // ...
    plugins: [
      // ...
      require('@tailwindcss/forms'),
    ],
  }
  ```
-->

<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-lg p-8 w-96">
            <h1 class="text-2xl font-semibold mb-4">新增商品資訊</h1>
            <form id="form" action="/api/products" method="POST">
                {{-- <form id="form" action="" method="POST"> --}}
                {{-- @csrf --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600">商品姓名</label>
                    <input type="text"
                        class="mt-1 p-2 w-full rounded-md border border-gray-300 focus:ring focus:ring-indigo-300">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600">商品價格</label>
                    <input type="text"
                        class="mt-1 p-2 w-full rounded-md border border-gray-300 focus:ring focus:ring-indigo-300">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600">商品描述</label>
                    <textarea class="mt-1 p-2 w-full rounded-md border border-gray-300 focus:ring focus:ring-indigo-300"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600">商品庫存</label>
                    <input type="text"
                        class="mt-1 p-2 w-full rounded-md border border-gray-300 focus:ring focus:ring-indigo-300">
                </div>
                <button type="submit"
                    class="bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring focus:ring-indigo-300">新增商品</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('#form');

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(form);

                fetch('/api/products', {
                        method: "POST",
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(response);
                        // alert('商品新增成功！');
                        form.reset();
                    })
                    .catch(error => {
                        // alert('商品新增成功');
                        console.log(error);
                    })
            });
        });
    </script>

</body>
