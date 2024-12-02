<?php 
    session_start();
    require_once($_SERVER["DOCUMENT_ROOT"]."/app/config/Directories.php");

    include(ROOT_DIR."app/config/DatabaseConnect.php");
    $db = new DatabaseConnect();
    $conn = $db->connectDB();

    //this variable will hold product data from db
    $carts    = [];
    $id         = @$_GET['id'];
    $userId     = $_SESSION["user_id"];
    $subtotal   = 0;
    $purchase_total = 0;
    try {
        
        $sql  = "SELECT carts.id,products.product_name,carts.quantity,carts.unit_price,carts.total_price "
                . " FROM carts "
                . " LEFT JOIN products ON products.id = carts.product_id"
                . " WHERE carts.user_id = $userId "; //select statement here
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $carts = $stmt->fetchAll();
        
    } catch (PDOException $e){
        echo "Connection Failed: " . $e->getMessage();
        $db = null;
    }


    require_once(ROOT_DIR."includes/header.php");

    if(isset($_SESSION["error"])){
        $messageErr = $_SESSION["error"];
        unset($_SESSION["error"]);
    }

    if(isset($_SESSION["success"])){
        $messageSucc = $_SESSION["success"];
        unset($_SESSION["success"]);
    }


?>
  

    <!-- Navbar -->
    <?php require_once(ROOT_DIR."includes/navbar.php"); ?>

    <!-- Shopping Cart -->
    <div class="container mt-5">
        <div class="row">
            <!-- Shopping Cart Items -->
            <div class="col-md-8">
                <h3>Shopping Cart</h3>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($carts){ 
                        foreach($carts as $indvCart){

                        ?>
                        <tr>
                            <td><?php echo $indvCart["product_name"]; ?></td>
                            <td><?php echo $indvCart["quantity"]; ?></td>
                            <td><?php echo number_format($indvCart["unit_price"],2); ?></td>
                            <td><?php echo number_format($indvCart["total_price"],2); ?></td>
                        </tr>
                        <?php 
                        //$subtotal = $subtotal + $indvCart["total_price"];
                        $subtotal += $indvCart["total_price"];
                        
                        } //end of foreach ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4">You do not have any products yet</td>    
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Cart Summary and Payment -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <?php if($carts){ ?>
                        <p>Subtotal: <span class="float-end">Php <?php echo number_format($subtotal,2); ?></span></p>
                        <p>Shipping: <span class="float-end">Php 50.00</span></p>
                        <hr>
                        <h5>Total: <span class="float-end"><?php echo number_format($subtotal + 50,2); ?></span></h5>

                        <!-- Payment Method Selection -->
                        <div class="mt-4">
                            <label for="paymentMethod" class="form-label">Select Payment Method</label>
                            <select class="form-select" id="paymentMethod" required>
                                <option value="credit">Credit/Debit Card</option>
                                <option value="paypal">PayPal</option>
                                <option value="gcash">GCash</option>
                            </select>
                        </div>

                        <!-- Payment Details -->
                        <div class="mt-3">
                            <label for="cardNumber" class="form-label">Card/Account Number</label>
                            <input type="text" class="form-control" id="cardNumber" placeholder="Enter your card or account number" required>
                        </div>

                        <!-- Confirm Payment Button -->
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success">Confirm Payment</button>
                        </div>
                        <?php } else { ?>
                            <p class="text-center">No product details yet</p>
                        <?php }?>    
                    </div>
                </div>
            </div>
        </div>
    </div>
    
   

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>