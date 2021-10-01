<?php
include "rest_get_customer_cards.php";
include_once 'countries.php';
include_once 'card_types.php';
////////////////////////////////////FUNCTIONS
function concatinateNameAddress($nameAddress){
    // return name and address string
    if(!isset($nameAddress->address2)){
        $nameAddress->address2 = "";
    }
    return xtrim($nameAddress->firstName, " ") .
            xtrim($nameAddress->lastName, "<BR>") .
            xtrim($nameAddress->address1, ", ") .
            xtrim($nameAddress->address2, ", ") .
            xtrim($nameAddress->locality, ", ") .
            xtrim($nameAddress->postalCode, ", ") .
            xtrim($nameAddress->country, ".");
}

function xtrim($in, $suffix){
    $out = trim($in);
    return (empty($out)? "" : $out . $suffix );
}
function stylePaymentInstrument($paymentInstrument){
    global $cardTypes;
    return
    "<div class=\"col-sm-2\"><img  src=\"images/". $cardTypes[$paymentInstrument->card->type]['image'] . "\" class=\"img-fluid\" alt=\"" . $cardTypes[$paymentInstrument->card->type]['alt'] . "\"></div>\n" .
    "<div class=\"col-sm-1\">\n" .
        "<ul class=\"list-unstyled\">" .
            "<li><strong>" . $paymentInstrument->_embedded->instrumentIdentifier->card->number . "</strong></li>\n" .
            "<li><small>Expires:&nbsp;" . $paymentInstrument->card->expirationMonth . "/" . $paymentInstrument->card->expirationYear . "</small></li>\n" .
        "</ul>\n" .
    "</div>\n";
}
///////////////////////////////////END FUNCTIONS
///////////////////////////////////VARIABLES
$count=0;
?>
<!DOCTYPE html>
<html lang="en-GB">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
        <title>Manage Your Cards</title>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>
    <body>
        <h5>Your Payment Cards</h5>
        <div class="accordion" id="accordionExample">
<?php       foreach ($paymentInstruments as $paymentInstrument): ?>
            <div class="accordion-item" id="<?php echo $paymentInstrument->id;?>_item">
                <h2 class="accordion-header" id="heading<?php echo $paymentInstrument->id;?>">
                    <button class="accordion-button <?php echo ($paymentInstrument->default?"":"collapsed");?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $paymentInstrument->id;?>" aria-expanded="true" aria-controls="collapse<?php echo $paymentInstrument->id;?>">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-2">
                                    <h5><?php $count++;echo ($paymentInstrument->default?"Default Card." :"Card #". $count );?></h5>
                                </div>
                                <?php echo stylePaymentInstrument($paymentInstrument);?>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapse<?php echo $paymentInstrument->id;?>" class="accordion-collapse collapse <?php echo ($paymentInstrument->default?"show":"");?>" aria-labelledby="heading<?php echo $paymentInstrument->id;?>" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                    <div class="row">
                        <div id="<?php echo $paymentInstrument->id;?>_buttons">
                            <div class="col-sm-6">
                                <button type="button" class="btn btn-link" onclick="usePaymentInstrument('<?php echo $paymentInstrument->id;?>')">Use this card</button>
                                <button type="button" class="btn btn-link" onclick="editPaymentInstrument('<?php echo $paymentInstrument->id;?>')">Edit Billing Address</button>
<?php if(!$paymentInstrument->default):?>
                            <button type="button" class="btn btn-link" onclick="updatePaymentInstrument('<?php echo $paymentInstrument->id;?>',true)">Set as default</button>
                            <button type="button" class="btn btn-link" onclick="deletePaymentInstrument('<?php echo $paymentInstrument->id;?>')">Remove</button>
<?php endif?>
                            </div>
                        </div>
                        <form id="<?php echo $paymentInstrument->id;?>_form" style="display: none">
                            <div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group form-floating mb-3">
                                            <input id="<?php echo $paymentInstrument->id;?>_firstName" type="text" class="form-control form-control-sm" value="<?php echo $paymentInstrument->billTo->firstName;?>" placeholder="First name" required>
                                            <label for="<?php echo $paymentInstrument->id;?>_firstName" class="form-label">First name*</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group form-floating mb-3">
                                            <input id="<?php echo $paymentInstrument->id;?>_lastName" type="text" class="form-control form-control-sm" value="<?php echo $paymentInstrument->billTo->lastName;?>" placeholder="Last Name" required>
                                            <label for="<?php echo $paymentInstrument->id;?>_lastName" class="form-label">Surname*</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group form-floating mb-3">
                                            <input id="<?php echo $paymentInstrument->id;?>_address1" type="text" class="form-control form-control-sm" value="<?php echo $paymentInstrument->billTo->address1;?>" placeholder="1st line of address" required>
                                            <label for="<?php echo $paymentInstrument->id;?>_address1" class="form-label">Address line 1*</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group form-floating mb-3">
                                            <input id="<?php echo $paymentInstrument->id;?>_address2" type="text" class="form-control form-control-sm" value="<?php echo (isset($paymentInstrument->billTo->address2)?$paymentInstrument->billTo->address2:"");?>" placeholder="2nd line of address">
                                            <label for="<?php echo $paymentInstrument->id;?>_address2" class="form-label">Address line 2</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group form-floating mb-3">
                                            <input id="<?php echo $paymentInstrument->id;?>_locality" type="text" class="form-control form-control-sm" value="<?php echo $paymentInstrument->billTo->locality;?>" placeholder="City/County" required>
                                            <label for="<?php echo $paymentInstrument->id;?>_locality" class="form-label">City/County*</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group form-floating mb-3">
                                            <input id="<?php echo $paymentInstrument->id;?>_postalCode" type="text" class="form-control form-control-sm" value="<?php echo $paymentInstrument->billTo->postalCode;?>" placeholder="Postcode" required>
                                            <label for="<?php echo $paymentInstrument->id;?>_postalCode" class="form-label">PostCode*</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group form-floating mb-3">
                                            <select id="<?php echo $paymentInstrument->id;?>_country" class="form-select">
<?php
foreach ($countries as $key => $value) {
    echo "<option value=\"". $key ."\"" . ( $paymentInstrument->billTo->country == $key? "selected": "") . ">" . $value . "</option>\n";
}
?>
                                            </select>
                                            <label for="<?php echo $paymentInstrument->id;?>_address_country" class="form-label">Country*</label>
                                        </div>
                                    </div>
                                </div>
                        *Required fields
                            </div>
<?php if(!$paymentInstrument->default):?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="<?php echo $paymentInstrument->id;?>_defaultCard" <?php echo ($paymentInstrument->default?"checked ":"");?>>
                                <label class="form-check-label" for="flexCheckDefault">Make this my default card</label>
                            </div>
<?php endif?>
                            <div class="row">
                                <div class="col-sm-1">
                                    <button type="button" class="btn btn-link" onclick="updatePaymentInstrument('<?php echo $paymentInstrument->id;?>',false)">Save</button>
                                </div>
                                <div class="col-sm-1">
                                    <button type="button" class="btn btn-link" onclick="toDo()">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
<?php endforeach; ?>
        <div class="row">
            <div class="col-sm-1">
                <button type="button" class="btn btn-link" onclick="cancel()">Cancel</button>
            </div>
        </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
    </body>
    <script>
    var customerId = "<?php echo $customerToken;?>";
<?php
foreach ($paymentInstruments as $paymentInstrument){
    echo "var paymentInstrument_". $paymentInstrument->id . " = '" . json_encode($paymentInstrument) . "';\n";
}
?>

    function editPaymentInstrument(id){
        document.getElementById(id+"_form").style.display = "block";
        document.getElementById(id+"_buttons").style.display = "none";
    }
    function updatePaymentInstrument(id, setDefaultOnly){
        console.log("\nUpdating Card: "+id);
        def = document.getElementById(id+"_defaultCard");
        if(def){
            // This card is NOT the default card
            defaultCard = def.checked;
        }else{
            // This card is currently the default card
            defaultCard = true;
        }
        firstName = document.getElementById(id+"_firstName").value;
        lastName = document.getElementById(id+"_lastName").value;
        address1 = document.getElementById(id+"_address1").value;
        address2 = document.getElementById(id+"_address2").value;
        locality = document.getElementById(id+"_locality").value;
        postalCode = document.getElementById(id+"_postalCode").value;
        country = document.getElementById(id+"_country").value;

        $.ajax({
            type: "POST",
            url: "rest_update_customer_payment_instrument.php",
            data: JSON.stringify({
                "setDefaultOnly": setDefaultOnly,
                "customerId": customerId,
                "paymentInstrumentId": id,
                "default": defaultCard,
                "firstName": firstName,
                "lastName": lastName,
                "address1": address1,
                "address2": address2,
                "locality": locality,
                "administrativeArea": "",
                "postalCode": postalCode,
                "country": country,
                "phoneNumber": ""
            }),
            success: function (result) {
                // Response is a json string - turn it into a javascript object
                let res = JSON.parse(result);
                console.log("\nUpdate:\n" + JSON.stringify(res, undefined, 2));
                let httpCode = res.httpCode;
                if (httpCode === "200") {
                    // Successfull response
                    location.reload();
                } else {
                    // 500 System error or anything else
                }
            }
        });
    }
    function usePaymentInstrument(id){
        xxx = window['paymentInstrument_'+id];
        parent.onPaymentInstrumentUpdated(id, JSON.parse(xxx));
    }
    function deletePaymentInstrument(id){
        console.log("\nDeleting Card: "+id);
        $.ajax({
            type: "POST",
            url: "rest_delete_customer_payment_instrument.php",
            data: JSON.stringify({
                "customerId": customerId,
                "paymentInstrumentId": id
            }),
            success: function (result) {
                // Response is a json string - turn it into a javascript object
                let res = JSON.parse(result);
                console.log("\nDelete:\n" + JSON.stringify(res, undefined, 2));
                let httpCode = res.httpCode;
                if (httpCode === "204") {
                    // Successfull response
                    location.reload();
                } else {
                    // 500 System error or anything else - TODO
                }
            }
        });
    }
    function cancel(){
        parent.onIframeCancelled();
    }
    </script>
</html>
