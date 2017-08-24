
![Frete Rápido - Sistema Inteligente de Gestão Logística](https://freterapido.com/imgs/frete_rapido.png)
<p>
    <a href="https://fr-slack.herokuapp.com" target="_blank">
        <img src="https://fr-slack.herokuapp.com/badge.svg">
    </a>
</p>
<hr>


### **Magento Platform Module**

Version: **1.2.0**
Compatibility: **1.9.x**

Useful links:

- [Magento Connect][1]
- [Administrative Panel][2]
- [support@freterapido.com][3]

Language: 

[<img src="https://lipis.github.io/flag-icon-css/flags/4x3/br.svg" alt="PT-BR" height="30px" title="Portuguese-BR"/>][6] [<img src="https://lipis.github.io/flag-icon-css/flags/4x3/gb.svg" alt="ENG" height="30px" title="English"/>][7]

----------

### How to Install

>**<i class="icon-attention"></i>ATTENTION!** We recommend you to do a back-up your store before any installation. The installation of this module in your store is the your sole responsibility.


- [Download the lastest version here][4],  unzip the file into "app" directory in your Magento store.
- Verify if the path **local.xml** is enabled in "app/etc" directory of your Magento store. Case this path is not enabled, you can rename the **local.xml.sample** file to **local.xml**.
- After all, you need to clear the cache of your Magento store by administrative area: System > Cache Management.

![Caution image](https://freterapido.com/dev/imgs/magento_doc_english/attention_installation.PNG)

----------

### Settings

Is needed setting up somethings on this module to get all usability with API of the **Frete Rápido**.


#### 1. Module settings:

- Now, you need to configure the new Shipping Method in: System > Settings > Shipping Methods > Frete Rápido (as the image below)

![Setting up the Frete Rápido module](https://freterapido.com/dev/imgs/magento_doc_english/fr_module_config.PNG "Configurações do módulo")

- **Enable:** Choose if you would like enable or disable the module in your store.
- **Title:** Allows to define a title over results section.
- **CNPJ:** Company registration on Brazil. The same code registered in Frete Rápido.
- **Weight format:** Choose the weight format used on your store (Kilogram ou grams).
- **Default Height (cm):** Define default height for all products that haven't this measurement informed.
- **Default Width (cm):** Define default width for all products that haven't this measurement informed.
- **Default Length (cm):** Define default lenght for all products that haven't this measurement informed.
- **Free shipping:** Choose if you would like enable or disable "Free Shipping" to the cheaper shipping option.
- **Minimum Value for Free Shipping:** Sets the minimum value (of cart) to activate the Free shipping rule. For undefined value, enter 0.
- **Results:** You can define wich filter you would like apply to receive the results of freight quotations.
- **Limit:** Allows you to limit until 20 results.
- **Token:** Integration token available in [Administrative Panel][2] > Company data > Integration

#### 2. Sender

- Information about the sender is very important to know the origin of your freight. You need to put the origin information in: System > Settings > Shipping Settings > Origin.

![Origin information](https://freterapido.com/dev/imgs/magento_doc_english/origin_settings.PNG "Origin information")

> **Note:** It's very important complete all fields correctly.

#### 3. Measures and Fabrication time:
- To calculate the freight correctly we need to know the measurements (cm) of each product. You need to put the measurements in: Catalog > Manage Products > [Select a product] > Frete Rápido menu.

![Setting up the measurements of a product](https://freterapido.com/dev/imgs/magento_doc_english/fr_products.PNG "Setting up the measurements of a product")

> **Attention:** You need to consider the measurements and weight of product with the package ready to shipment.
> 
> It's mandatory each product has their weight configured to calculate the freight correctly. If the measurements of a product are blank, will be considered the default measures information in the setting-up of the module. But it's recommended each product has their own weight and measures informed.

#### 4. Categories
- It's important to relate each category from your store with the categories of the Frete Rápido in: Catalog> Manage Categories> Frete Rápido Categories.

![Setting up categories](https://freterapido.com/dev/imgs/magento_doc_english/fr_categories.PNG)

> **Note:** Not all categories in your store may match with the Frete Rápido categories, but you can relate them broadly.
>
> **Example 1**: Women's Clothing -> Clothing
>
> **Example 2**: CDs -> CD / DVD / Blu-Ray
>
> **Example 3**: Guitars -> Musical Instruments

--------

### Hiring the Freight
It's possible to contract the freight directly in the administrative area of the store, in the detailing of the customer's orders.

* Open the order (Sales > Orders) and click on the **"Ship"** button.
![Contracting freights](https://freterapido.com/dev/imgs/magento_doc_english/order.png "Detailing of order")

* You will be redirected to the shipping confirmation screen. After you checking the information, click on the button **"Submit Shipment"**
![Freight confirmation](https://freterapido.com/dev/imgs/magento_doc_english/confirm_order.PNG "Freight confirmation")
* At this moment, the Frete Rápido will request the collect of your items at the transportation carrier chosen.

--------

### Freight quotation at product page
![Setting-up freight quotation at product page](https://freterapido.com/dev/imgs/magento_doc_english/fr_product_page.PNG "Setting-up freight quotation at product page")

- **Enable:** You can enable ou disable the freight quotation at the product page.
- **Display position:** It allows you choose position of the quotation bloque on the product page layout (this option will depends of the store layout). The options are:
	- Right Column: displays at right column (if any).
	- Left Column: displays at left column (if any).
	- Additional information block: displays after the description block.
	- Custom layout: allows you to customize the position of the block in the field **Alias display block**.

- **Relative position:** Allows you to choose the position of the block relative to the other blocks of the page.
- **Alias display block:** Allows you specify a block to be changed for the freight quotation block.

--------

### General observations:
1. To calculate freight with Correios (brazilian Post Office service) you can setting-up your contract data in the [Administrative Panel][2] > Integrations > Correios - SIGEP WEB.
2. This module does only quotation for personal destination.

--------

### Contributions
Did you find any bug? Do you have any improvements suggestions? Great! Don't be shy, send to us a pull request with your changes and help this project get even better.

1. Do a "Fork"
2. Create a branch to your feature: ` $ git checkout -b feature/nova-funcionalidade`
3. Do a commit with your changes: ` $ git commit -am "adiciona nova funcionalidade"`
4. Make the push of the branch: ` $ git push origin feature/nova-funcionalidade`
5. Make a new **Pull Request** in GitHub.

--------

### Licence
[MIT][5]


[1]: https://www.magentocommerce.com/magento-connect/catalogsearch/result/?q=frete+r%C3%A1pido&pl=0 "Magento Connect"
[2]: https://freterapido.com/painel/?origin=github_magento "Painel do Frete Rápido"
[3]: mailto:support@freterapido.com ":)"
[4]: https://github.com/freterapido/freterapido_magento/archive/master.zip
[5]: https://github.com/freterapido/freterapido_magento/blob/master/LICENSE
[6]: https://github.com/freterapido/freterapido_magento/blob/master/README.md
[7]: https://github.com/freterapido/freterapido_magento/blob/master/README_EN.md
