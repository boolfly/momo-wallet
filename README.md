# Momo Payment for Magento 2

![Momo logo](https://developers.momo.vn/images/logo.png)

https://techcrunch.com/2019/01/18/momo-warburg-pincus/

Visit developer page: https://developers.momo.vn/

> MoMo Payment Platform API is a payment solution for business units, allowing customers to use MoMo E-Wallet account to pay for services on various platforms: Desktop Website, Mobile Website, Mobile Application, POS, Pay In Bill, In App MoMo.
This extension will bring Momo to Magento 2 platform.

## Installation

##### Using Composer (we recommended)

```
composer require boolfly/module-momo-wallet
```

## Setup Currency

First of all, we need to make sure our website supporting Vietnamese Dong. 

Log in to Admin, **STORES > Configurations > GENERAL > Currency Setup > Currency Options > Allowed Currencies**. Make sure the Vietnamese Dong is selected.

![Momo Wallet currency](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/momo-wallet/momo-wallet-currency-01.png)

Go to Currency Rates, **STORES > Currency > Currency Rates**

![Momo Wallet currency](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/momo-wallet/momo-currency-rates-01.png)

## Momo Configuration

Log in to Admin, **STORES > Configurations > SALES > Payment Methods > Momo**

![Momo Wallet Configuration](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/momo-wallet/momo-wallet-01.png)

You can read more here: https://developers.momo.vn/#/docs/en/?id=integration-information

Configuration info to integrate with MoMo API.
<ul>
   <li>Enabled: enable or disable this method.</li>
   <li>Partner Code: Identify your bussiness account.</li>
   <li>Access Key: Access key server.</li>
   <li>Secret Key: Used to create digital signature.</li>
   <li>Public Key: Used to encrypt data by RSA algorithm.</li>
  <li>Sandbox Mode: when testing, we should enable this mode</li>
 </ul>
 
 ## Checkout
 After enabling this method, go to the checkout, we can see this method.
 
 ![Momo Wallet Checkout](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/momo-wallet/momo-wallet-02.png)
 
 Momo Payment page:
 
 ![Momo Wallet Checkout](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/momo-wallet/momo-wallet-03.png)
 
 ## Purchased Successfully
 
  ![Momo Wallet](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/momo-wallet/momo-wallet-04.png)
  
  ![Momo Wallet](https://github.com/boolfly/wiki/blob/master/magento/magento2/images/momo-wallet/momo-wallet-05.png)
  
  
Contribution
---
Want to contribute to this extension? The quickest way is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests)

Support
---
If you encounter any problems or bugs, please open an issue on [GitHub](https://github.com/boolfly/momo-wallet/issues).

Need help setting up or want to customize this extension to meet your business needs? Please email boolfly.inc@gmail.com and if we like your idea we will add this feature for free or at a discounted rate.
