<nav class="main-header navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        @if (request()->is('maps'))
            <a href="{{ url('/') }}" >
                <span class="">
                    <img src="{{ asset('/img/Mahalaxmilogo.png') }}" alt="Municipality Logo" id="map-logo"
                        style="line-height: .8; margin-left: 0.1rem; margin-right: 0.1rem; margin-top:2px; max-height:33px; width:auto
                </span>
            </a>

        @endif

        @if (request()->is('maps'))
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button" onclick="hideImage()">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button" onclick="toggleElements()">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        @endif

        <!-- This div is used for aligning the user name and roles to the right -->
        <div style="flex-grow: 1;"></div> <!-- This pushes content to the right -->

        <!-- Display the user's name and roles on the right side -->
        <div style="display: flex; justify-content: flex-end; margin-top: 0.5%;">
            <small>Hi,{{ Auth::user()->name }}, {{ implode(', ', get_current_user_roles()) }}</small>
        </div>

        <li class="nav-item ml-auto">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
    </ul>
</nav>

<script>
    function hideImage() {
        var logo = document.getElementById('map-logo');
        if (logo.style.display === 'none') {
            logo.style.display = 'inline';
        } else {
            logo.style.display = 'none';
            helloText.style.display = 'inline';
        }
    }
</script>
