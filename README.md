## Gamespace

<img src="./images/gamespace_nograd.png" width = "420" height = "250"/>

## Introduction

Gamespace is a social media web application made by gamers for gamers.
	
## Usage

<details>
<summary><strong>Set up local server</strong></summary>
<br />

### 1. Install XAMPP

[XAMPP Official dowload](https://www.apachefriends.org/download.html)

### 2. Start local server

For Windows: <br />&emsp;1.  Open the Xampp control panel and start the Apache and MySQL services
<br /><br />For Mac:<br />&emsp;1.  Open XAMPP
<br />&emsp;2.  In general, click "start"
<br />&emsp;3.  In Services, start MySQL and Apache
<br />&emsp;4. In Network, Enable Localhost:8080
<br />

### 3. Clone project code

Locate the htdocs folder found at: 
<br />&emsp;Windows: C:\xampp\htdocs
<br />&emsp;Mac: Open XAMPP click->Volumes->Mount->Explore

Clone repository into new folder "gamespace" inside htdocs folder:
<br /><br />Using Git bash:
<br />&emsp;Start Git bash from inside htdocs folder and run
```bash
git clone https://github.com/itws2110section2group7/gamespace.git gamespace
```
Alternatively:
<br />&emsp;[Repository link](https://github.com/itws2110section2group7/gamespace)
<br />&emsp;- Click Code->Download Zip
<br />&emsp;- Extract the zip file into the htdocs folder
<br />&emsp;- Rename folder "gamespace-main" to "gamespace"
</details>

<details>
<summary><strong>Set up Mysql DB</strong></summary>
<br />

### 1. Log into phpMyAdmin

[http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)

1. Click Import->Chose File and pick file schema.sql in /gamespace/db/
2. Click Go
3. Wait until import has finished
</details>

<details>
<summary><strong>Navigate to website</strong></summary>

### 1. Go to website

In your broswer navigate to page:
<br />&emsp;Mac: [http://localhost:8080/gamespace/index.php](http://localhost:8080/gamespace/index.php)
<br />&emsp;Windows: [http://localhost/gamespace/index.php](http://localhost/gamespace/index.php)

### 2. Final notes

You can create a new account or login
<br />For you convenience, test accounts and data have been included:
<table>
<tr>
<th>Username</th>
<th>Password</
</tr>
<tr>
<td>carter.d.ellis10@gmail.com</td>
<td>myPa$$longlonglong21</td>
</tr>
<tr>
<td>vna@gmail.com</td>
<td>wuGdfTebSkk8giV</td>
</tr>
<tr>
<td>will@gmail.com</td>
<td>wuGdfTebSkk8giV</td>
</tr>
</table>

If you would like to clear this test data and start anew:
<br />&emsp;1. Go to phpMyAdmin
<br />&emsp;2. Click on database "gamespace"
<br />&emsp;3. On every table click Operations->Empty the table (TRUNCATE)->Uncheck Enable foreign key checks
<br />&emsp;4. Delete all files in the /gamespace/uploads folder
</details>
