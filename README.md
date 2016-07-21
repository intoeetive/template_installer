### Template Installer for ExpressionEngine 2.x

Transform source HTML files to EE templates with one click. Quick start for site development - essential for every new build!
Upload the HTML / CSS / JavaScript files you received after converting your design (PSD) to web files to any folder on your server using FTP management program.
Put the (absolute) server path to directory in the “Path to directory” field and press Install Templates.
The module will create template groups based on sub-folders in the folder provided in “Path to directory” field. Any files in the root will be ignored. Any files in subfolders nested inside subfolders will be ignored. File that can’t be converted to templates (images etc.) will be ignored.
Webpage and static text files will be converted to templates named after file name without extension; other file types will be converted to template name after full file name (with extension)
If there is subfolder named ‘site’, the template group created out of it will be used as site default.
The example structure of uploaded folder can look like this:
/site/ 
——/index.html
/news/
——/index.html
——/view.html
/css/
——/default.css
/js/
——/script.js
It will be converted to following templates structure:
-site
——index
-news
——index
——view
-css
——default.css
-js
——scripts.js