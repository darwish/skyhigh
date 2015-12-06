<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Retail Renegade - Savings Just For You</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/stylish-portfolio.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

	<link rel="stylesheet" href="../css/jquery.growl.css">
	
	<script src="//cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.3/handlebars.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="../js/vendor/jquery.growl.js"></script>

	<script src="../js/plugins.js"></script>
	<script src="../js/main.js"></script>
	
	
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Header -->
    <header id="top" class="header">
        <div class="text-vertical-center">
            <h1 class="white-title">Retail Renegade</h1>
            <h3 class="white-title">The savings you want</h3>
            <br>
            <a href="#about" class="btn-light btn-lg">Find Out More</a>
        </div>
    </header>

    <!-- About -->
    <section id="about" class="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>Save on what YOU want to buy</h2>
                    <p class="lead">Search for things you know you want, and stores in your area will make a coupon, personalized just for you.</p>
                </div>
				<div class="col-lg-12 text-center">
					<form method="post" action="../register.php" class="form-inline" data-toggle="ajax" data-form="register">
						<div class="form-group">
							<label for="username" class="control-label">Email</label>
							<input type="text" class="form-control" name="username" id="username">
						</div>

						<div class="form-group">
							<label for="password" class="control-label">Password</label>
							<input type="password" class="form-control" name="password" id="password">
						</div>

						<button class="btn btn-primary">Register</button>
					</form>
				</div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>

    <!-- Services -->
    <!-- The circle icons use Font Awesome's stacked icon classes. For more information, visit http://fontawesome.io/examples/ -->
    <section id="services" class="services bg-primary">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-10 col-lg-offset-1">
                    <h2>What We Offer</h2>
                    <hr class="small">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="service-item">
                                <span class="fa-stack fa-4x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-dollar fa-stack-1x text-primary"></i>
                            </span>
                                <h4>
                                    <strong>Personalized Coupons</strong>
                                </h4>
                                <p>Retailers generate coupons just for you.</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="service-item">
                                <span class="fa-stack fa-4x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-shopping-cart fa-stack-1x text-primary"></i>
                            </span>
                                <h4>
                                    <strong>Local Convenience</strong>
                                </h4>
                                <p>Find stores closest to you with what you want.</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="service-item">
                                <span class="fa-stack fa-4x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-cc-mastercard fa-stack-1x text-primary"></i>
                            </span>
                                <h4>
                                    <strong>Easy Payment</strong>
                                </h4>
                                <p>Use your MasterCard for quick payments.</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="service-item">
                                <span class="fa-stack fa-4x">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-bar-chart fa-stack-1x text-primary"></i>
                            </span>
                                <h4>
                                    <strong>Vendor Analytics</strong>
                                </h4>
                                <p>Simple tools for shops to analyze how much savings to pass on to you.</p>
                            </div>
                        </div>
                    </div>
                    <!-- /.row (nested) -->
                </div>
                <!-- /.col-lg-10 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>

    <!-- Callout -->
    <aside class="callout">
        <div class="text-vertical-center">
            <h1>Happy Shopping</h1>
        </div>
    </aside>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Custom Theme JavaScript -->
	
	<script>
	$(document).off('form-success');
	$(document).on('form-success', '[data-form="register"]', function(e, response) {
		location = '../index.php';
	});
	</script>

</body>

</html>
