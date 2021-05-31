<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{env('APP_URL')}}/asset/js/jquery-3.5.1.min.js"></script>
<script src="{{env('APP_URL')}}/asset/js/popper.min.js"></script>
<script src="{{env('APP_URL')}}/asset/js/bootstrap.min.js"></script>
<script src="{{env('APP_URL')}}/asset/js/jquery.simple-checkbox-table.min.js"></script>
<script src="{{env('APP_URL')}}/datatables/jquery.dataTables.min.js"></script>
<script src="{{env('APP_URL')}}/datatables/dataTables.bootstrap4.min.js"></script>
<script src="{{env('APP_URL')}}/asset/js/bootstrap-select.min.js"></script>
<script>
    function gen() {
        let pass = [];
        let letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r',
            's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
        ];
        let nums = [1, 2, 3, 4, 5, 6, 7, 8, 9];

        let sym = ['!', '@', '#', '$', '%', '&', '*'];
        for (let i = 0; i < 3; i++) {
            pass.push(letters[Math.floor(Math.random() * letters.length - 1)]);
        }

        for (let j = 0; j < 3; j++) {
            pass.push(nums[Math.floor(Math.random() * nums.length - 1)]);
        }

        pass.push(sym[Math.floor(Math.random() * sym.length - 1)]);

        for (let x = 0; x < 2; x++) {
            pass.push(letters[Math.floor(Math.random() * letters.length - 1)]);
        }

        let re = pass.join("");
        document.getElementById('pass').value = re;
        pass = [];
    }

    $(document).ready(function () {

        if($('#msg_success').val() != ''){
            setTimeout(function() {
                $('.msg_success').hide();
            }, 3000);
        }

        $('.div_fa_list').click(function () {
            $('body').toggleClass('open-menu-1');
        });

        var dropdown = document.getElementsByClassName("dropdown-btn");
        var i;

        for (i = 0; i < dropdown.length; i++) {
            dropdown[i].addEventListener("click", function () {
                if ($('.sidenav a').hasClass('active')) {
                    $('.sidenav a.input_active').removeClass('active');
                }
                this.classList.toggle("active");
                var dropdownContent = this.nextElementSibling;
                if (dropdownContent.style.display === "block") {
                    dropdownContent.style.display = "none";
                } else {
                    dropdownContent.style.display = "block";
                }

                var url = window.location.pathname;
                if (url == '/user_management' || url == '/edit_user') {
                    $('.sidenav .dropdown-container #user_mang').addClass('menu_active_links');
                    $('.sidenav .dropdown-container #add_new_user').removeClass('menu_active_links');

                }else if(url == '/add_user'){
                    $('.sidenav .dropdown-container #add_new_user').addClass('menu_active_links');
                    $('.sidenav .dropdown-container #user_mang').removeClass('menu_active_links');
                }
            });
        }

    });
</script>
