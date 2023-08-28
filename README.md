# Remita OpenCart 3.x Payment Gateway

---

- [Overview](#Overview)
- [Features](#Features)
- [Installation](#Installation)
- [Setup](#Setup)
- [Contributing](#Contributing)
---

### Overview
Enhance your OpenCart webshop with the Remita OpenCart Payment Plugin. This solution empowers store administrators to integrate a diverse range of payment methods effortlessly. For a comprehensive overview of features and services, please visit [Remita's official website](https://www.remita.net).

![](payment-image.png) 

---

### Features

*   __Accept payment__ via Visa Cards, Mastercards, Verve Cards and eWallets

* 	__Seamless integration__ Effortlessly integrates into the OpenCart checkout page.
* 	__Add Naira__ currency symbol. You can conveniently add the Naira currency symbol. To do so, navigate to your OpenCart Admin and click on  __System > Localisation > Currencies__ from the left-hand menu.

---

### Installation

1. Download the Remita plugin zip file.

2. Extract the downloaded file and copy the extracted admin and catalogue folders into the "upload" folder of your OpenCart installation.

3. Log in to your OpenCart Admin through your browser. Click on Extensions > Extensions from the left-hand menu.

4. Select Payments in the "Choose the extension type" dropdown.

5. Scroll to "Remita" and click the Install button (the "+" sign will change to "-").

### Setup

1. Login to your OpenCart Admin through your browser. Click on "Extensions > Payments" from the left-hand menu

2. Scroll to "Remita" and click the Edit button beside the installation button "+" sign
3. Enter the public key and secrete key. You can find these keys in the [Remita Gateway Admin Panel](https://login.remita.net/remita/registration/signup.spa)
4. Set the "Status" drop-down to "Enabled" ( It is disabled on default )
5. Save the settings
6. A success message will be displayed to confirm that the plugin has been installed and the setup was successful


## Useful links
* Join our Slack Developer/Support channel at [slack](http://bit.ly/RemitaDevSlack)
    
## Support
- For all other support needs, support@remita.net

### Contributing
- To contribute to this repo, create an issue on what you intend to fix or update, make a PR and team will look into it and merge.

1. Fork the repository
2. Create a new branch: `git checkout -b feature-name`
3. Make changes and commit: `git commit -m "added some new features"`
4. Make pushes: `git push origin feature-name`
5. Submit a PR (Pull Request)
