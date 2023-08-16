<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>

<body>
    <div class="flex">
        <!-- 左侧导航栏 -->
        <nav class="flex w-40 bg-gray-950 text-white  justify-center ">
            <ul class="space-y-5 text-center">
                <li><a href="#" class="hover:text-gray-300">新增商品</a></li>
                <li><a href="#" class="hover:text-gray-300">查看購物車</a></li>
                {{-- <li><a href="#" class="hover:text-gray-300"></a></li> --}}
                <li><a href="#" class="hover:text-gray-300">聯絡我們</a></li>
            </ul>
        </nav>

        <!-- 主要内容区域 -->
        <main class="flex-1">
            {{-- <main class="hover:flex-1 p-4"> --}}
            <div class="bg-contain bg-center text-red-300 text-center py-5"
                style="background-image: url('https://tailwindui.com/img/ecommerce-images/product-page-01-related-product-01.jpg')">

                <h1 class="text-center text-5xl italic text-gray-600 font-sans dark:md:hover:text-fuchsia-600 ">
                    good job online shop
                </h1>
                <h1 class="text-4xl font-bold">good code</h1>
                <p class="text-lg">And I'm a Back End Enginee</p>

            </div>


            <div class="bg-white">
                <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
                    <h3 class="text-xl font-bold tracking-tight text-gray-900">Customers also purchased</h3>

                    <div id="product-list"
                        class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4 xl:gap-x-8">
                            {{-- <div class="group relative">
                                <div
                                    class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-md bg-gray-200 group-hover:opacity-75 lg:w-60">

                                    <img src="https://tailwindui.com/img/ecommerce-images/product-page-01-related-product-01.jpg"
                                        alt="Front of men&#039;s Basic Tee in black."
                                        class="h-60 w-60 object-cover object-center"> 
                                </div> 

                                <div class="mt-4 flex justify-between">
                                    <div>
                                        <h3 class="text-sm text-gray-700">
                                            <a href="#">
                                                <span aria-hidden="true" class="absolute inset-0"></span>
                                                Basic Tee
                                            </a>
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500">Black</p>
                                        <p class="text-xl font-medium text-gray-900">$35</p>
                                    </div>
                                </div>
                            </div> --}}
                    </div>
                </div>

            </div>

        </main>

    </div>
    <footer class="h-28 footer flex bg-gray-500  items-center justify-center">
        <div class="text-2xl font-bold tracking-tight text-white">
            <p>自我嘗試全端開發 2023 </p>
        </div>
        <!-- A footer for the page. -->
    </footer>

    <script>
        fetch('/api/products')
            .then(res => res.json()) //把它轉成porimse
            //getData 是可以取的變數,意味著res.json()這包資料
            .then(getData => {
                //賦予變數 , 去抓 id 等於 product-list 的位置
                const productList = document.getElementById('product-list');
                //這個data指的是 laravel response 最外層的那個data
                getData.data.forEach(product => { //product 指的是迴圈跑完之後的結果
                    const productElement = createProductElement(product);
                    productList.appendChild(productElement);
                });
            })
            .catch(error => console.log('圖片有問題'));


        function createProductElement(product) {
            //創建一個div空元素
            const productElement = document.createElement('div');
            //className 是指 為這個DIV 補上一個 calss屬性 名為product
            productElement.className = 'product';

            const imageElement = document.createElement('img');
            //把物件的東西取出來並賦值
            imageElement.src = product.images[0].image;
            imageElement.alt = product.name;

            const nameElement = document.createElement('h3');
            nameElement.className = 'product-name';
            nameElement.textContent = product.name;

            const priceElement = document.createElement('p');
            priceElement.className = 'product-price';
            priceElement.textContent = `$${product.price}`;

            productElement.appendChild(imageElement);
            productElement.appendChild(nameElement);
            productElement.appendChild(priceElement);

            return productElement;
        }
    </script>

</body>

</html>

</body>

</html>
