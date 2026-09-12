<DOCTYPE html>
<html>

    <head>
            <title>Pawprint</title>
            <link rel="stylesheet" type="text/css" href="index.css">
    </head>

    <body>

        <script src="index.js"></script>
        
        <header class="site-header">
            <div class="nav">

                <div class="logo">
                    <a href="index.php">
                        <img src="images/logo.png" alt="Pawprint Logo">
                    </a>
                </div>

                <nav class="nav-links">
                    <a href="#" class="active">Home</a>
                    <a href="shop.php">Shop</a>
                    <a href="about-us.php">About Us</a>
                    <a href="contact.php">Contact</a>
                </nav>

                <div class="nav-icons">
                    <a href="cart.php">
                        <img src="images/cart.png" alt="Cart">
                    </a>
                    <a href="account.php">
                        <img src="images/acc.png" alt="Account">
                    </a>
                </div>
            </div>
        </header>

        <section class="hero">
            <div class="hero-content">

                <h1>
                    Happy Pets,<br>
                    <span>Happy Hearts.</span>
                </h1>

                <p class="hero-description">
                    High-quality pet food, fun toys, and<br>
                    essential accessories for a healthier,<br>
                    happier life together.
                </p>

                <a href="shop.php" class="hero-button">
                    Shop Now
                </a>
            </div>

            <div class="hero-image">
                <img src="images/hero.png" alt="Pets">
            </div>
        </section>

        <section class="categories">

            <div class="categories-heading">
                <img src="images/pawprint.png" alt="" class="section-pawprint">
                <h2>Everything for Every Paw</h2>
                <p>Explore our carefully selected categories.</p>
            </div>

            <div class="category-grid">

                <a href="shop.php?category=Pet%20Food" class="category-card category-food">
                    <h3>Pet Food</h3>
                    <div class="category-content">
                        <div class="category-image">
                            <img src="images/dog-bowl.png" alt="Pet Food">
                        </div>
                        <div class="category-details">
                            <p>Nutritious meals for a healthy life.</p>
                            <span class="category-arrow">></span>
                        </div>
                    </div>
                </a>

                <a href="shop.php?category=Toys" class="category-card category-toys">
                    <h3>Toys</h3>
                    <div class="category-content">
                        <div class="category-image">
                            <img src="images/ball.png" alt="Pet Toys">
                        </div>
                        <div class="category-details">
                            <p>Fun and engaging toys for happy pets.</p>
                            <span class="category-arrow">></span>
                        </div>
                    </div>
                </a>

                <a href="shop.php?category=Accessories" class="category-card category-accessories">
                    <h3>Accessories</h3>
                    <div class="category-content">
                        <div class="category-image">
                            <img src="images/collar.png" alt="Pet Accessories">
                        </div>
                        <div class="category-details">
                            <p>Essentials for comfort, safety, and style.</p>
                            <span class="category-arrow">></span>
                        </div>
                    </div>
                </a>
            </div>
        </section>

        <section class="bestsellers">

            <div class="bestsellers-heading">
                <img src="images/pawprint.png" alt="" class="bestsellers-pawprint">
                <h2>Our Bestsellers</h2>
                <p>Loved by pets, Trusted by pet parents</p>
            </div>

            <div class="product-grid">

                <!-- PRODUCT 1 -->
                <div class="product-card">

                    <div class="product-image">
                        <img src="images/petfood.png" alt="Pawprint Dog Food">
                    </div>

                    <div class="product-info">
                        <h3>Pawprint Dog Food</h3>

                        <div class="product-rating">
                            <span class="stars">★★★★★</span>
                            <span class="reviews">(149)</span>
                        </div>

                        <p class="product-price">₱499.00</p>

                        <form method="POST" action="add-to-cart.php" class="bestseller-cart-form">
                            <input type="hidden" name="product_id" value="1">
                            <input type="hidden" name="quantity" value="1">

                            <button type="submit" class="add-cart">
                                Add to Cart
                            </button>
                        </form>
                    </div>

                </div>


                <!-- PRODUCT 2 -->
                <div class="product-card">

                    <div class="product-image">
                        <img src="images/pettoy.png" alt="Pawprint Chew Toy">
                    </div>

                    <div class="product-info">
                        <h3>Pawprint Chew Toy</h3>

                        <div class="product-rating">
                            <span class="stars">★★★★★</span>
                            <span class="reviews">(103)</span>
                        </div>

                        <p class="product-price">₱199.00</p>

                        <form method="POST" action="add-to-cart.php" class="bestseller-cart-form">
                            <input type="hidden" name="product_id" value="3">
                            <input type="hidden" name="quantity" value="1">

                            <button type="submit" class="add-cart">
                                Add to Cart
                            </button>
                        </form>
                    </div>

                </div>


                <!-- PRODUCT 3 -->
                <div class="product-card">

                    <div class="product-image">
                        <img src="images/petcollar.png" alt="Pawprint Pet Collar">
                    </div>

                    <div class="product-info">
                        <h3>Pawprint Pet Collar</h3>

                        <div class="product-rating">
                            <span class="stars">★★★★★</span>
                            <span class="reviews">(86)</span>
                        </div>

                        <p class="product-price">₱349.00</p>

                        <form method="POST" action="add-to-cart.php" class="bestseller-cart-form">
                            <input type="hidden" name="product_id" value="5">
                            <input type="hidden" name="quantity" value="1">

                            <button type="submit" class="add-cart">
                                Add to Cart
                            </button>
                        </form>
                    </div>

                </div>


                <!-- PRODUCT 4 -->
                <div class="product-card">

                    <div class="product-image">
                        <img src="images/petbed.png" alt="Pawprint Pet Bed">
                    </div>

                    <div class="product-info">
                        <h3>Pawprint Pet Bed</h3>

                        <div class="product-rating">
                            <span class="stars">★★★★★</span>
                            <span class="reviews">(56)</span>
                        </div>

                        <p class="product-price">₱1,299.00</p>

                        <form method="POST" action="add-to-cart.php" class="bestseller-cart-form">
                            <input type="hidden" name="product_id" value="7">
                            <input type="hidden" name="quantity" value="1">

                            <button type="submit" class="add-cart">
                                Add to Cart
                            </button>
                        </form>
                    </div>

                </div>

            </div>
        </section>

        <section class="promise">

            <img src="images/pawprint.png"  alt="" class="promise-pawprint">

            <div class="promise-container">

                <div class="promise-image">
                    <img src="images/girldog.png" alt="Pet owner with dog">
                </div>

                <div class="promise-content">
                    <h2>
                        More Than Products,<br>
                        It’s Our Promise.
                    </h2>
                    <p class="promise-description">
                        At Pawprint, we believe pets are family. That's why we offer only the best quality, safety, and care for every tail wag and purr.
                    </p>

                    <div class="promise-features">

                        <div class="promise-feature">
                            <div class="feature-icon">
                                <img src="images/shield-ico.png" alt="Quality First">
                            </div>

                            <div class="feature-text">
                                <h3>Quality First</h3>
                                <p>
                                    Carefully selected products you can trust.
                                </p>
                            </div>
                        </div>

                        <div class="promise-feature">
                            <div class="feature-icon">
                                <img src="images/cat-ico.png" alt="Happy and Healthy Pets">
                            </div>

                            <div class="feature-text">
                                <h3>Happy & Healthy Pets</h3>
                                <p>
                                    Better products ffor a better life.
                                </p>
                            </div>
                        </div>

                        <div class="promise-feature">
                            <div class="feature-icon">
                                <img src="images/heart-ico.png" alt="Made with Love">
                            </div>

                            <div class="feature-text">
                                <h3>Made with Love</h3>
                                <p>
                                    Products chosen to bring comfort and joy.
                                </p>
                            </div>
                        </div>


                        <!-- For Pet Parents -->
                        <div class="promise-feature">
                            <div class="feature-icon">
                                <img src="images/people-ico.png" alt="For Pet Parents">
                            </div>

                            <div class="feature-text">
                                <h3>For Pet Parents</h3>
                                <p>
                                    Designed to make your life easier.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="testimonials">

            <img src="images/pawprint.png" alt="" class="testimonials-pawprint">

            <div class="testimonials-heading">
                <h2>What Pet Parents Say</h2>
                <p>Real stories from our happy community.</p>
            </div>

            <div class="testimonial-slider">

                <div class="testimonial-slide active">

                    <div class="testimonial-card">
                        <span class="quote">“</span>
                        <p>My dog loves the food! His coat has never been shinier.</p>
                        <div class="testimonial-person">
                            <img src="images/person1.png" alt="Clythe S.">
                            <div>
                                <h3>Clythe S.</h3>
                                <span>Dog Person</span>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card">
                        <span class="quote">“</span>
                        <p>The toys are super durable and keep my cat active!</p>
                        <div class="testimonial-person">
                            <img src="images/person2.png" alt="Mikaela A.">
                            <div>
                                <h3>Mikaela A.</h3>
                                <span>Cat Mom</span>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card">
                        <span class="quote">“</span>
                        <p>Great accessories and fast delivery. Highly recommended!</p>
                        <div class="testimonial-person">
                            <img src="images/person3.png" alt="Chrysler S.">
                            <div>
                                <h3>Chrysler S.</h3>
                                <span>Bird Guy</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="testimonial-slide">
                    <div class="testimonial-card">
                        <span class="quote">“</span>
                        <p>Amazing products and excellent quality. My dog absolutely loves everything we buy here.</p>
                        <div class="testimonial-person">
                            <img src="images/person4.png" alt="">
                            <div>
                                <h3>Sarah M.</h3>
                                <span>Dog Mom</span>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card">
                        <span class="quote">“</span>
                        <p>Pawprint has made shopping for my pets so much easier and more enjoyable.</p>
                        <div class="testimonial-person">
                            <img src="images/person5.png" alt="">
                            <div>
                                <h3>James R.</h3>
                                <span>Pet Dad</span>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card">
                        <span class="quote">“</span>
                        <p>Great products, affordable prices, and my pets are happier than ever.</p>
                        <div class="testimonial-person">
                            <img src="images/person6.png" alt="">
                            <div>
                                <h3>Anna L.</h3>
                                <span>Cat Mom</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-slide">
                    <div class="testimonial-card">
                        <span class="quote">“</span>
                        <p>I love how carefully selected the products are. Everything feels high quality.</p>
                        <div class="testimonial-person">
                            <img src="images/person7.png" alt="">
                            <div>
                                <h3>Cath M.</h3>
                                <span>Dog Mom</span>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card">
                        <span class="quote">“</span>
                        <p>My cat loves the toys and I love how simple it is to find exactly what I need.</p>
                        <div class="testimonial-person">
                            <img src="images/person8.png" alt="">
                            <div>
                                <h3>Lisa P.</h3>
                                <span>Cat Mom</span>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-card">
                        <span class="quote">“</span>
                        <p>Pawprint is now one of my favorite places to shop for my pets.</p>
                        <div class="testimonial-person">
                            <img src="images/person9.png" alt="">
                            <div>
                                <h3>Daniel K.</h3>
                                <span>Pet Parent</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="testimonial-dots">

                <button 
                    type="button"
                    class="testimonial-dot active"
                    data-slide="0"
                    aria-label="Show testimonials 1"
                ></button>

                <button 
                    type="button"
                    class="testimonial-dot"
                    data-slide="1"
                    aria-label="Show testimonials 2"
                ></button>

                <button 
                    type="button"
                    class="testimonial-dot"
                    data-slide="2"
                    aria-label="Show testimonials 3"
                ></button>

            </div>
        </section>

        <footer class="footer">

            <div class="footer-container">
                <div class="footer-brand">

                    <img src="images/logo-white.png" alt="Pawprint" class="footer-logo">
                    <p>
                        Quality pet food, toys, and accessories<br>
                        made for happy pets and happier<br>
                        pet parents.
                    </p>

                    <div class="footer-socials">
                        <a href="https://youtube.com" aria-label="YouTube">
                            <img src="images/yt-ico.png" alt="YouTube">
                        </a>
                        <a href="https://facebook.com" aria-label="Facebook">
                            <img src="images/fb-ico.png" alt="Facebook">
                        </a>
                        <a href="https://instagram.com" aria-label="Instagram">
                            <img src="images/ig-ico.png" alt="Instagram">
                        </a>
                        <a href="https://tiktok.com" aria-label="TikTok">
                            <img src="images/tk-ico.png" alt="TikTok">
                        </a>
                    </div>
                </div>

                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <a href="shop.php">Shop</a>
                    <a href="about-us.php">About Us</a>
                    <a href="contact.php">Contact Us</a>
                </div>

                <div class="footer-column">
                    <h3>Customer Care</h3>
                    <a href="account.php">My Account</a>
                    <a href="terms&privacy.php">Terms & Conditions</a>
                    <a href="terms&privacy.php">Privacy Policy</a>
                </div>

                <div class="footer-newsletter">
                    <h3>Stay in the Loop</h3>
                    <p>
                        Get updates on new products,<br>
                        exclusive deals, and pet care tips!
                    </p>
                    <form class="subscribe-form">

                        <input type="email" placeholder="Enter you email" required>
                        <button type="submit">
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
            <img src="images/pawprint-brown.png" alt="" class="footer-paw">

        </footer>

    </body>
</html> 
