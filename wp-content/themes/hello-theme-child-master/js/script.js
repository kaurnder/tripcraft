  //login logout
document.addEventListener("DOMContentLoaded", function () {

        isLoggedIn = ajax_object.isUserLogin;
        val = ajax_object.val;
        let loginBtn = document.querySelector(".login-btn");
        // let signInBtn = document.querySelector(".sign-in-btn");
        let logoutBtn = document.querySelector(".logout-btn");
        
        console.log('isLoggedIn:', isLoggedIn);
        console.log('vall:', val);

        if (loginBtn && logoutBtn) {
            if (isLoggedIn === "true") {
                loginBtn.style.display = "none";
                // signInBtn.style.display = "none";
                logoutBtn.style.display = "inline-block";
            } else {
                loginBtn.style.display = "inline-block";
                // signInBtn.style.display = "inline-block";
                logoutBtn.style.display = "none";
            }
        } else {
            console.error("One or more buttons are not found in the DOM");
        }

        
       
   jQuery(document).ready(function($) {

            /*fixed header */
            $(window).scroll(function() {
                if ($(this).scrollTop() > 1){  
                $('.my-custom-header').addClass("sticky");
                } else {
                $('.my-custom-header').removeClass("sticky");
                }
            });

            // recently Blog search functionality
            $('#ajax-post-search').on('keyup', function(e) {
            e.preventDefault();

            let searchVall = $('#ajax-post-search').val();

            $.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'search_blog_posts',
                    search: searchVall,
                },
                success: function(response){
                    console.log('result search',response.data.msg);
                    if (response.success) {
                        $('#ajax-post-results').html(response.data.msg);
                        $('#msg').html('');
                        } else {
                            $('#ajax-post-results').html('');
                            $('#msg').html(response.data.msg);
                        }
                }
            });
            });


            //tour search functionality using ajax    
            $('#ajax-post-tour-search').on('keyup', function(e) {
            e.preventDefault();

            let search_tour = $('#ajax-post-tour-search').val();
            $.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'search_tour_packages',
                    search_tour: search_tour,
                },
                success: function(response){
                    console.log('result',response.data.message);
                    if (response.success) {
                        $('#ajax-post-tour-results').html(response.data.msg);
                        $('#msg').html('');
                        } else {
                            $('#ajax-post-tour-results').html('');
                            $('#msg').html(response.data.msg);
                        }
                }
            });
            });

            //destination search functionality using ajax    
            $('#ajax-post-destination-search').on('keyup', function(e) {
            e.preventDefault();
            let search_destination = $('#ajax-post-destination-search').val();
            $.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'search_destination_packages',
                    search_destination: search_destination,
                },
                success: function(response){
                    console.log('result',response.data.message);
                    if (response.success) {
                        $('#ajax-post-destination-result').html(response.data.message);
                        $('#message').html('');
                        } else {
                            $('#ajax-post-destination-result').html('');
                            $('#message').html(response.data.message);
                        }
                }
            });
            });

          //common ajax blog post search and pagination
          let currentPage = 1;
          function fetch_blog_posts(page = 1) {
                let searchVal = $('#ajax-blogpost-search').val();
                let texonomy = $('#destination_val').val();
                let Culture_text = $('#texonomy_val').val();
               
                console.log('my textnomy',texonomy);
               
                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'search_blog_posts_ajax',
                        search_blog: searchVal,
                        paged: page,
                        my_texonomy : texonomy,
                        culture_text: Culture_text
                    },
                    success: function(response) {
                        console.log(response.data.message);
                        if(response.success){
                            $('#ajax-blogpost-results').html(response.data.message);
                            $('#message').html('');
                        }
                        else{
                            $('#ajax-blogpost-results').html('');
                            $('#message').html(response.data.message);
                        } 
                    }
                });
            }

            $('#ajax-blogpost-search-btn').on('submit', function (e) {
                e.preventDefault();
                currentPage = 1;
                fetch_blog_posts(currentPage);
            });

            $(document).on('click', '.blog-pagination a', function (e) {
                e.preventDefault();
                const page = parseInt($(this).text()) || 1;
                fetch_blog_posts(page);
            });


            //destination search functionality using ajax 
            let currpage = 1;
            function prcatics_blog(pagess=1){
            let ival = $('#val_blog').val();
            $.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'blog_practics',
                    input_val: ival,
                    my_page : pagess
                },
                success: function(response){
                    if (response.success) {
                        $('#ajax-practics-results').html(response.data.blog_msg);
                        $('#blog_msg').html('');
                        } else {
                            $('#ajax-practics-results').html('');
                            $('#blog_msg').html(response.data.blog_msg);
                        }
                }
            });
        }
       
            $('#p_blog').on('submit', function(e) {
            e.preventDefault();
            prcatics_blog(currpage);
            });

             $(document).on('click', '.blog-pagination-practics a', function (e) {
                e.preventDefault();
                const pagessi = parseInt($(this).text()) || 1;
                console.log('pages',pagessi);
              prcatics_blog(pagessi);
            });
        });
            //services post type serach js
            $('#ajax-services-search').on('keyup', function(e) {
            e.preventDefault();
            let search_services = $('#ajax-services-search').val();
            $.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'search_ace_services_posts',
                    search_services: search_services
                },
                success: function(response){
                    //console.log('result search',response.data.msg);
                    if (response.success){
                        $('#ajax-services-post-results').html(response.data.ace_msg);
                        $('#ace_msg').html('');
                        } else {
                            $('#ajax-services-post-results').html('');
                            $('#ace_msg').html(response.data.ace_msg);
                        }
                }
            });
            });
               });  
            
            
            



   

//google reviws
jQuery(document).ready(function($) {
    $('.testimonial-slider').slick({
        slidesToShow: 1,
        arrows: true,
        dots: true,
        autoplay: true,
        autoplaySpeed: 4000
    });
});



        
        //Blog post search using fetch api javascript
        // const inputsearch = document.getElementById("ajax-blogpost-search");
        // const resultpost = document.getElementById("ajax-blogpost-results");
        // const form = document.getElementById("ajax-blogpost-search-btn");
        //    form.addEventListener("submit", function(e) {
        //     e.preventDefault();
        //     const searchval = inputsearch.value;
        //     let url = ajax_object.ajaxurl;
        //     const formData = new FormData();
        //     formData.append('action', 'search_blog_posts_ajax');
        //     formData.append('search_blog', searchval);
        //     fetch(url,{
        //         method: 'POST',
        //         body: formData
        //     })
        //     .then(response => response.text())
        //     .then(data => {
        //         resultpost.innerHTML = data;
        //           console.log(response.data.message);
        //     });
        // });


        //blog post search using ajax
        // jQuery(document).ready(function($) {
        //     $('#ajax-blogpost-search-btn').on('submit', function(e) {
        //     e.preventDefault();

        //     let searchVal = $('#ajax-blogpost-search').val();
        //     $.ajax({
        //         url: ajax_object.ajaxurl,
        //         type: 'POST',
        //         data: {
        //             action: 'search_blog_posts_ajax',
        //             search_blog: searchVal,
        //         },
        //         success: function(response){
        //             console.log('result',response.data.message);
        //             if (response.success) {
        //                 $('#ajax-blogpost-results').html(response.data.message);
        //                 $('#message').html('');
        //                 } else {
        //                     $('#ajax-blogpost-results').html('');
        //                     $('#message').html(response.data.message);
        //                 }
        //         }
        //     });
          //  });
       // });

        

 


{/* login js <script>
document.addEventListener("DOMContentLoaded", function () {
    let isLoggedIn = <?php echo is_user_logged_in() ? 'true' : 'false'; ?>;
    let loginBtn = document.querySelector(".login-btn");
    let signInBtn = document.querySelector(".sign-in-btn");
    let logoutBtn = document.querySelector(".logout-btn");
    if (isLoggedIn) {
        // Hide login and sign-in buttons
        loginBtn.style.display = "none";
        signInBtn.style.display = "none";
        // Show logout button
        logoutBtn.style.display = "inline-block";
    } else {
        // Show login and sign-in buttons
        loginBtn.style.display = "inline-block";
        signInBtn.style.display = "inline-block";
        // Hide logout button
        logoutBtn.style.display = "none";
    }
});
</script> */}

