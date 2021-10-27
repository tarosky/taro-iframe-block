# Taro iframe Block

Tags: gutenberg, block editor, iframe  
Contributors: tarosky, Takahashi_Fumiki  
Tested up to: 5.7  
Requires at least: 5.4  
Requires PHP: 5.6  
Stable Tag: nightly  
License: GPLv3 or later  
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

Add iframe block for your editor. Responsive and keeping aspect ratio.

## Description

Add iframe block for block editor.
WordPress editor sometimes clean up <code>iframe</code> tag if the user is an contributor(single site) or an editor(multi site) who has no capability [unfiltered_html](https://wordpress.org/support/article/roles-and-capabilities/#unfiltered_html).

This plugin simply add 1 custom block **iframe block**. That's all and no config.

### Features

- `iframe` tag not escaped.
- Responsive supported.
- Keep aspect ratio. Default is <code>16:9</code>.
- Keep `iframe` unesacaped even in multisite. If you have multiple  writers in your site, this might help without any roles-and-capabilitie knowledge.
- Align full and align wide supported.

## Installation

### From Plugin Repository

Click install and activate it.

### From Github

See [releases](https://github.com/tarosky/taro-iframe-block/releases).

## FAQ

### Where can I get supported?

Please create new ticket on support forum.

### How can I contribute?

Create a new [issue](https://github.com/tarosky/taro-iframe-block/issues) or send [pull requests](https://github.com/tarosky/taro-iframe-block/pulls).

## Changelog

### 1.0.3

* Add quick hack for JS translation in GlotPress([detail](https://wordpress.slack.com/archives/C02RP50LK/p1635254887019500))

### 1.0.2

* Fix JS translation.

### 1.0.0

* First release.
