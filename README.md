
# Useful SEO Functions by SyntaxSurge

This plugin provides a suite of SEO and Security functions to enhance the functionality of your WordPress site. It includes features like SERPcraft.com Autoposting, Affiliate Settings, Security Settings, and comprehensive SEO Settings.

## Table of Contents

- [Plugin Overview](#plugin-overview)
- [Installation](#installation)
   * [Automatic Installation (Recommended)](#automatic-installation-recommended)
   * [Manual Installation](#manual-installation)
   * [Installation using Git](#installation-using-git)
      * [1. Installation of Git](#1-installation-of-git)
         + [For CentOS Linux distribution](#for-centos-linux-distribution)
         + [For Ubuntu/Debian-based Linux distributions](#for-ubuntudebian-based-linux-distributions)

      * [2. GitHub Repository Management](#2-github-repository-management)
         + [OPTION #1. INITIALIZE and PUSH files to GitHub (Sender)](#option-1-initialize-and-push-files-to-github-sender)
         + [OPTION #2. INITIALIZE the files by using the CLONE command from GitHub (Receiver)](#option-2-initialize-the-files-by-using-the-clone-command-from-github-receiver)
            - [Clone Files to your Server](#clone-files-to-your-server)
            - [Clone Files to your Local Computer](#clone-files-to-your-local-computer)

         + [OPTION #3. To PUSH file changes to GitHub (Sender)](#option-3-to-push-file-changes-to-github-sender)
            - [Push Files to Repository from Server](#push-files-to-repository-from-server)
            - [Push Files to Repository from Local Computer](#push-files-to-repository-from-local-computer)

         + [OPTION #4. Update or PULL files (Receiver)](#option-4-update-or-pull-files-receiver)
            - [Pull Files from Repository to Server](#pull-files-from-repository-to-server)
            - [Pull Files from Repository to Local Computer](#pull-files-from-repository-to-local-computer)

- [Plugin Features](#plugin-features)
   * [SERPcraft.com Autoposting](#serpcraftcom-autoposting)
   * [Affiliate Settings](#affiliate-settings)
   * [Security Settings](#security-settings)
   * [SEO Settings](#seo-settings)

- [Contributing](#contributing)

## Plugin Overview

**Useful SEO Functions by SyntaxSurge** is designed to enhance your WordPress site's SEO and security capabilities. It offers a range of functionalities, from automating affiliate links to improving the visibility of your posts on search engines.

## Plugin Features

### SERPcraft.com Autoposting

This feature automates the process of posting content to your WordPress site from SERPcraft.com. It streamlines the content publication workflow, ensuring timely and efficient updates to your site.

- **Select User:**

   - Description: Choose the WordPress user account under which the autoposted content will be published. This ensures that all auto-generated posts are attributed to a specific user, maintaining consistency and accountability.
   - Usage: From the dropdown menu, select the WordPress user who will be the author of the autoposted content.

- **Application Name:**

   - Description: Define the name of the application or service that will be used for autoposting. This is typically the identifier for the SERPcraft.com integration, helping to manage and track the source of autoposted content.
   - Usage: Enter the name of the application or service in the provided field. This name is used for identification and management purposes.

- **Generate Password:**

   - Description: Create a secure, unique password for the SERPcraft.com integration. This password is used to authenticate the autoposting process, ensuring secure and authorized access to your WordPress site.
   - Usage: Click on the 'Generate Password' button to create a new password. This password will be used for the secure authentication of the autoposting service. Make sure to save this password securely, as it is crucial for the integration's security.

### Affiliate Settings

- Adds your Amazon affiliate ID to all Amazon links.
- Auto-inserts product card shortcode below the header.
- Replaces existing affiliate tags if not desired.

### Security Settings

- Disable public view of WordPress API for unauthorized users.

### SEO Settings

- Enables Rank Math Meta Description of Posts via API.
- Shortcodes for structuring FAQs to schema for rich results.
- Auto-update of Rank Math focus keyword and meta description.

## Installation

This section provides a step-by-step guide on how to install the **Useful SEO Functions by SyntaxSurge** plugin on your WordPress site.

### Automatic Installation (Recommended)

1. **Login to your WordPress Dashboard:**
   Navigate to your WordPress site’s admin area by typing in `yourdomain.com/wp-admin` in your browser's address bar and logging in with your username and password.

2. **Access the Plugin Installation Page:**
   Once you are logged in, navigate to `Plugins > Add New` in the left-hand sidebar menu.

3. **Search for the Plugin:**
   Use the search bar on the top right of the Add Plugins page and type in “Useful SEO Functions by SyntaxSurge.”

4. **Install the Plugin:**
   You will see the plugin listed in the search results. Click the `Install Now` button next to the plugin name.

5. **Activate the Plugin:**
   Once the installation is complete, the `Install Now` button will change to `Activate`. Click this button to activate the plugin on your website.

6. **Configure Plugin Settings:**
   After activation, you may need to configure the plugin settings according to your preferences. Find the plugin settings by navigating to the SEO or Settings menu in your WordPress dashboard, or the plugin may add its own menu item. Follow the on-screen instructions to configure your SERPcraft.com Autoposting, Affiliate Settings, Security Settings, and SEO Settings.


### Manual Installation

1. **Download the Plugin:**
   Download the plugin zip file from the WordPress Plugin Directory or the official website of **Useful SEO Functions by SyntaxSurge**. [Download the latest version of the plugin HERE](https://serpcraft.tawk.help/article/download-wordpress-plugin-for-serpcraftcom)

2. **Upload the Plugin:**
   From your WordPress Dashboard, navigate to `Plugins > Add New`. There, you will find an option to `Upload Plugin` next to the page title. Click it.

3. **Choose the Downloaded File:**
   Click on `Choose File` and select the zip file you've previously downloaded, then click `Install Now`.

4. **Activate the Plugin:**
   After the installation process is complete, click on `Activate Plugin` to start using the plugin on your WordPress site.

5. **Configure Plugin Settings:**
   Once activated, proceed to the plugin's settings page as mentioned in the automatic installation method and customize the settings as needed.


### Installation using Git

#### 1. Installation of Git

If you haven't installed Git yet, follow these steps to install it:

##### For CentOS Linux distribution:

```bash
sudo yum update -y && sudo yum install git -y
```

##### For Ubuntu/Debian-based Linux distributions:

```bash
sudo apt-get update && sudo apt-get install git -y
```

#### 2. GitHub Repository Management

##### OPTION #1. INITIALIZE and *PUSH* files to GitHub (Sender)

- If you have not registered your GitHub's email and password in your terminal yet:

```bash
git config --global user.email "syntaxsurge@gmail.com" && git config --global user.name "syntaxsurge"
```

- To initialize and push files to your GitHub Repository, first navigate inside the folder of your wordpress using `cd` command, for example, if you are using `aapanel`, you can run this command to change directory:

```bash
cd /www/wwwroot/example.com/wp-content/plugins/useful-seo-functions-by-syntaxsurge
```

**To push the files to GitHub:**

- You can do the same code in your local computer or server, just make sure you navigate to the directory where the source code is saved.

```bash
REPO_NAME='useful-seo-functions-by-syntaxsurge'
git init && \
git add . && \
git commit -m "first commit" && \
git remote add origin https://github.com/syntaxsurge/$REPO_NAME.git && \
git branch -M main && \
git push -u origin main
```

##### OPTION #2. INITIALIZE the files by using the *CLONE* command from GitHub (Receiver)

###### Clone Files to your Server

- Navigate first to the plugin folder, if you are using `aapanel`, you can run this command to change directory and create the missing directories if not yet exist:

```bash
dir='/www/wwwroot/example.com/wp-content/plugins/useful-seo-functions-by-syntaxsurge'; [ -d "$dir" ] || sudo -u www mkdir -p "$dir" && cd "$dir"
```

- To delete all subdirectories and files, including the hidden ones, but exclude the parent directory (..), from the current directory before cloning it:

```bash
sudo rm -rf * .[^.]*
```

- To clone the repository, perform the following steps:
   1. __Assign Repository Name to a Variable__: Set the variable `REPO_NAME` to 'useful-seo-functions-by-syntaxsurge'.
   2. **Change Ownership of Directory**: Change the ownership of the `/home/www` directory to the user and group `www`.
   3. **Configure Git Credential Helper**: Configure Git to use the `store` credential helper globally, which stores GitHub username and password so that you don't need to enter it every time you push or pull.
   4. **Clone Repository**: Clone the repository from GitHub into the `/home/www` directory as the `www` user.
   5. **Move Repository Files**: Move all the files from the cloned repository's directory to its parent directory.
   6. **Remove Cloned Repository Directory**: Remove the cloned repository directory, as it is now empty.
   7. **Change File Permissions**: Change the permissions of the `.git` directory to `700` to make it executable and accessible to the owner only, and set the permissions of `README.md` to `600` to restrict access to the owner only.
   8. **Setup Credential Store**: Set the permissions of the `.git-credentials` file (which stores the GitHub credentials) to `600` to restrict access to the owner only.

**Here's the corresponding script to `CLONE` the Repository:**

```bash
REPO_NAME='useful-seo-functions-by-syntaxsurge'
sudo chown -R www:www /home/www && \
sudo -u www git config --global credential.helper store && \
sudo -u www git clone https://github.com/syntaxsurge/$REPO_NAME.git && \
mv $REPO_NAME/* $REPO_NAME/.[^.]* . && \
rmdir $REPO_NAME && \
chmod -R 700 .git && \
chmod -R 600 README.md && \
chmod 600 /home/www/.git-credentials && \
sudo chown -R www:www .git/
```

When prompted, provide your GitHub credentials. This command configures Git to store your credentials, clones the latest files from GitHub, and ensures your credentials are securely stored with restricted permissions.

###### Clone Files to your Local Computer

- To save the username and password and clone the repository:

```bash
git config --global credential.helper store && git clone https://github.com/syntaxsurge/useful-seo-functions-by-syntaxsurge.git
```

- To clone the repository only:

```bash
git clone https://github.com/syntaxsurge/useful-seo-functions-by-syntaxsurge.git
```

##### OPTION #3. To *PUSH* file changes to GitHub (Sender)

- This is Applicable only after Git initialization.
- You should change the `commit files` message to something descriptive:

###### Push Files to Repository from Server:

- Register your GitHub's email and password in your terminal first using `www` user from your server

```bash
sudo -u www git config --global user.email "syntaxsurge@gmail.com" && sudo -u www git config --global user.name "syntaxsurge"
```

- Push the files using `www` user from your server:

```bash
git add . && git commit -m "commit files" && sudo -u www git push -u origin main
```

###### Push Files to Repository from Local Computer

```bash
git add . && git commit -m "commit files" && git push -u origin main
```

##### OPTION #4. Update or *PULL* files (Receiver)

- This is Applicable only after Git initialization or you already cloned the repository before.

###### Pull Files from Repository to Server

- To update the repository, if there are new updates, use:

```bash
sudo chown -R www:www .git/ && \
sudo -u www git pull && \
chmod -R 600 README.md
```

However, if you updated some files in your current git directory, To temporarily store changes you've made in your working directory when you don't want to commit them yet, run these commands:

```bash
sudo chown -R www:www .git/ && \
git stash && \
sudo -u www git pull && \
chmod -R 600 README.md
```

This ensures the modified files remain only on your local machine and are not reflected in the GitHub repository. If you wish to update the saved repository on GitHub, you should commit the changes and then push them.

###### Pull Files from Repository to Local Computer

- To pull the latest updates in the repository:

```bash
git pull
```

## Contributing

We welcome contributions to improve this plugin. If you have suggestions or modifications, please feel free to submit a pull request.

To submit a pull request:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Make your changes.
4. Commit your changes (`git commit -am 'Add some feature'`).
5. Push to the branch (`git push origin feature-branch`).
6. Create a new Pull Request.

Your contributions and suggestions are greatly appreciated!
