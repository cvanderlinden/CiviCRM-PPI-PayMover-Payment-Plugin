 <?php
/* Author: 		Craig Vanderlinden (cvanderlinden)
** Created Date:	January 3 2011
** Last Modified:	March 1 2012
** Purpose:		Installing PayMover to CiviCRM
** Notes:		PPI PayMover Version 12.10.0
**			CiviCRM Version 3.4
**			Drupal Version 6
*/


require_once 'CRM/Core/Payment.php';
include("PayMover/Paygateway.php");

class CRM_Core_Payment_PayMover extends CRM_Core_Payment 
{ 
   const
      CHARSET  = 'UTF-8'; # (not used, implicit in the API, might need to convert?)
         
   /** 
    * We only need one instance of this object. So we use the singleton 
    * pattern and cache the instance in this variable 
    * 
    * @var object 
    * @static 
    */ 
   static private $_singleton = null; 

   /**********************************************************
    * Constructor 
    *
    * @param string $mode the mode of operation: live or test
    * 
    * @return void 
    **********************************************************/

   function __construct( $mode, &$paymentProcessor ) 
   {
       $this->_mode             = $mode;             // live or test
       $this->_paymentProcessor = $paymentProcessor;
       $this->_processorName    = ts('PayMover');
   }

   /** 
     * singleton function used to manage this object 
     * 
     * @param string $mode the mode of operation: live or test
     *
     * @return object 
     * @static 
     * 
     */ 
    static function &singleton( $mode, &$paymentProcessor ) {
        $processorName = $paymentProcessor['name'];
        if (self::$_singleton[$processorName] === null ) {
            self::$_singleton[$processorName] = new CRM_Core_Payment_PayMover( $mode, $paymentProcessor );
        }
        return self::$_singleton[$processorName];
    }

    /** 
     * This function checks to see if we have the right config values 
     * 
     * @return string the error message if any 
     * @public 
     */ 
    function checkConfig( ) {
        $config = CRM_Core_Config::singleton( );

        $error = array( );

        if ( empty( $this->_paymentProcessor['user_name'] ) ) {
            $error[] = ts( 'Token is not set in the Administer CiviCRM &raquo; Payment Processor.' );
        }
        
        if ( ! empty( $error ) ) {
            return implode( '<p>', $error );
        } else {
            return null;
        }
    }
	
	/** 
     * This function does the payment
     * 
     * @return string the error message if any 
     * @public 
     */ 
	function doDirectPayment( &$params ) 
	{
		// TOKEN needed to send transaction data
		$token = "TEST";
		
		// Create new Transaction Request
		$creditCardRequest = new TransactionRequest();

		// Apply Token
		$creditCardRequest->setAccountToken($token);
		
		// No Recurring Payments for now
		if ($params['is_recur'] == true) {       
			CRM_Core_Error::fatal( ts( 'PayMover - recurring payments not implemented' ) );
		}
		
		/* -- CHARGE TOTAL -- */
		
		$chargeTotal = $params['amount'];
		if($chargeTotal != "") {
			if(!$creditCardRequest->setChargeTotal($chargeTotal)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- EMAIL -- */
		
		$billEmail = $params['email'];
		if($billEmail != "") {
			if(!$creditCardRequest->setBillEmail($billEmail)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- CHARGE TYPE -- */
		
		$chargeType = "SALE";
		if ($chargeType != "") {
			if (!$creditCardRequest->setChargeType($chargeType)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- CARD NUMBER -- */
		
		$creditCardNumber = $params['credit_card_number'];
		if($creditCardNumber != "") {
			if(!$creditCardRequest->setCreditCardNumber($creditCardNumber)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- SECURITY CODE -- */
		
		$creditCardVerificationNumber = $params['cvv2'];
		if($creditCardVerificationNumber != "") {
			if(!$creditCardRequest->setCreditCardVerificationNumber($creditCardVerificationNumber)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- EXPERATION MONTH -- */
		
		$expireMonth = $params['month'];
		if($expireMonth != "") {
			if(!$creditCardRequest->setExpireMonth($expireMonth)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- EXPERATION YEAR -- */
		
		$expireYear = $params['year'];
		if($expireYear != "") {
			if(!$creditCardRequest->setExpireYear($expireYear)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- BILLING FIRST NAME -- */
		
		$billFirstName = $params['first_name'];
		if($billFirstName != "") {
			if(!$creditCardRequest->setBillFirstName($billFirstName)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- BILLING MIDDLE NAME -- */
		if (strlen($params['middle_name']) > 0 ){
			$billMiddleName = $params['middle_name'];
			if($billMiddleName != "") {
				if(!$creditCardRequest->setBillMiddleName($billMiddleName)) {
					$error[] = ts($creditCardRequest->getError());
				}
			}
		}
		
		/* -- BILLING LAST NAME -- */
		
		$billLastName = $params['last_name'];
		if($billLastName != "") {
			if(!$creditCardRequest->setBillLastName($billLastName)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- STREET ADDRESS -- */
		
		$billAddressOne = $params['street_address'];
		if($billAddressOne != "") {
			if(!$creditCardRequest->setBillAddressOne($billAddressOne)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- CITY -- */
		
		$billCity = $params['city'];
		if($billCity != "") {
			if(!$creditCardRequest->setBillCity($billCity)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- COUNTRY -- */
		
		$billCountryCode = $params['country']; 
		if($billCountryCode != "") {
			if(!$creditCardRequest->setBillCountryCode($billCountryCode)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- PROVINCE/STATE -- */
		
		$billStateOrProvince = $params['state_province'];
		if($billStateOrProvince != "") {
			if(!$creditCardRequest->setBillStateOrProvince($billStateOrProvince)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- POSTAL CODE -- */
		
		$billZipOrPostalCode = $params['postal_code'];
		if($billZipOrPostalCode != "") {
			if(!$creditCardRequest->setBillZipOrPostalCode($billZipOrPostalCode)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- INVOICE ID -- */
		
		$invoiceNumber = $params['invoiceID'];
		if($invoiceNumber != "") {
			if(!$creditCardRequest->setInvoiceNumber($invoiceNumber)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- ORDER ID -- */
		
		$orderID = substr($params["invoiceID"], -9);
		if($orderID != "") {
			if(!$creditCardRequest->setOrderID($orderID)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- TRANSACTION CONDITION CODE -- */
		
		$transactionConditionCode = "5";		// SECURE ECOMMERCE
		if($transactionConditionCode != "") {
			if(!$creditCardRequest->setTransactionConditionCode($transactionConditionCode)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- ORDER DESCRIPTION -- */
		$orderDescription = $params['description'];
		if($orderDescription != "") {
			if(!$creditCardRequest->setOrderDescription($orderDescription)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- INDUSTRY-- */
		$industry = "RETAIL";
		if ($industry != "") {
			if (!$creditCardRequest->setIndustry($industry)) {
				$error[] = ts($creditCardRequest->getError());
			}
		}
		
		/* -- DO THE TRANSACTION -- */
		
		$creditCardResponse = $creditCardRequest->doTransaction();
		
		/* -- STORE RESPONCE -- */
		
		$params['trxn_id'] = $creditCardResponse->GetISOCode();
		
		$params['trxn_result_code'] = $creditCardResponse->GetResponseCode();
		
		if($creditCardResponse->GetResponseCode() != 1){
			CRM_Core_Error::fatal( ts( '<br> RESPONCE CODE: ' . $creditCardResponse->GetResponseCode() . '
		<br> TOKEN:
		<br> ADDRESS: ' . $billAddressOne . '
		<br> POSTAL CODE: ' . $billZipOrPostalCode . '
		<br> CHARGE TOTAL: ' . $chargeTotal . '
		<br> CHARGE TYPE: ' . $chargeType . '
		<br> CC NUMBER: ' . $creditCardNumber . '
		<br> CVV: ' . $creditCardVerificationNumber . '
		<br> MONTH: ' . $expireMonth . '
		<br> YEAR: ' . $expireYear . '
		<br> INDUSTRY: ' . $industry . '
		<br> ORDERID: ' . $orderID . '
		<br> TCC: ' . $transactionConditionCode ) );
		}

		return $params;
	}

}
