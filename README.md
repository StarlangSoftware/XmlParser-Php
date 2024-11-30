For Developers
============
You can also see [C++](https://github.com/starlangsoftware/XmlParser-CPP), [C](https://github.com/starlangsoftware/XmlParser-C), [Js](https://github.com/starlangsoftware/XmlParser-Js), or [Java](https://github.com/starlangsoftware/XmlParser) repository.

## Requirements

* [Php 8.0 or higher](#php)
* [Git](#git)

### Php 

To check if you have a compatible version of Php installed, use the following command:

    php -V
    
You can find the latest version of Php [here](https://www.php.net/downloads/).

### Git

Install the [latest version of Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git).

## Download Code

In order to work on code, create a fork from GitHub page. 
Use Git for cloning the code to your local or below line for Ubuntu:

	git clone <your-fork-git-link>

A directory called XmlParser will be created. Or you can use below link for exploring the code:

	git clone https://github.com/starlangsoftware/XmlParser-Php.git

## Open project with PhpStorm IDE

Steps for opening the cloned project:

* Start IDE
* Select **File | Open** from main menu
* Choose `XmlParser-Php` file
* Select open as project option
* Couple of seconds, dependencies will be downloaded. 

Detailed Description
============

In order to load an xml document, we use the constructor

    $doc = new XmlDocument($fileName)
    
and parse with the parse method

    $doc->parse()
    
Root node of the document can be obtained via the getFirstChild method:

    $rootNode = $doc->getFirstChild()
  
For example, to iterate over the first level tags in the xml file one can use

    $rootNode = $doc->getFirstChild()
    $childNode = $rootNode->getFirstChild()
    while ($childNode){
      ...
      $childNode = $childNode->getNextSibling()
    }

Tag name can be obtained via getName, pcData via getPcData methods.
