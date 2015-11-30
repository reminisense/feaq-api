define({ "api": [
  {
    "type": "post",
    "url": "/login",
    "title": "User Login",
    "name": "Login",
    "group": "Authentication",
    "version": "1.0.0",
    "examples": [
      {
        "title": "Example Usage",
        "content": "http://api.featherq.com/login",
        "type": "js"
      }
    ],
    "description": "<p>Checks for the user in the database and returns an access key.</p> ",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>The unique access key sent by the client.</p> "
          }
        ]
      }
    },
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "fb_id",
            "description": "<p>User's Facebook id provided by the Facebook javascript API.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "click_source",
            "description": "<p>The location of the button (e.g. <code>landing_page_top_right</code>), where the user logged in.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "success",
            "description": "<p>Returns <code>1</code> if the login is successful.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "accessToken",
            "description": "<p>The access key to remember the session.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"success\": 1,\n    \"accessToken\": \"123123drink123123drink\"\n}",
          "type": "Json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error": [
          {
            "group": "Error",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "success",
            "description": "<p>Returns <code>0</code> if the login fails.</p> "
          },
          {
            "group": "Error",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "MissingValue",
            "description": "<p>Missing <code>fb_id</code> upon request.</p> "
          },
          {
            "group": "Error",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "SignUpRequired",
            "description": "<p>User with the given <code>fb_id</code> has not yet registered to Featherq.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Error-response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"success\": 0,\n    \"err_code\": \"SignUpRequired\"\n}",
          "type": "Json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AuthenticationController.php",
    "groupTitle": "Authentication"
  },
  {
    "type": "get",
    "url": "/logout",
    "title": "User Logout",
    "name": "Logout",
    "group": "Authentication",
    "version": "1.0.0",
    "examples": [
      {
        "title": "Example Usage",
        "content": "http://api.featherq.com/logout",
        "type": "js"
      }
    ],
    "description": "<p>Forgets the user's session from the app.</p> ",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>The unique access key sent by the client.</p> "
          }
        ]
      }
    },
    "permission": [
      {
        "name": "none"
      }
    ],
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "success",
            "description": "<p>Returns <code>1</code> if the logout is successful.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"success\": 1\n}",
          "type": "Json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AuthenticationController.php",
    "groupTitle": "Authentication"
  },
  {
    "type": "post",
    "url": "/user/register",
    "title": "User Registration",
    "name": "Register",
    "group": "Authentication",
    "version": "1.0.0",
    "examples": [
      {
        "title": "Example Usage",
        "content": "http://api.featherq.com/user/register",
        "type": "js"
      }
    ],
    "description": "<p>Registers a new user to the database.</p> ",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>The unique access key sent by the client.</p> "
          }
        ]
      }
    },
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "accessToken",
            "description": "<p>The facebook access token (provided by the Facebook javascript API).</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "fb_id",
            "description": "<p>User's facebook id (provided by the Facebook javascript API).</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "fb_url",
            "description": "<p>User's facebook url (provided by the Facebook javascript API).</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "first_name",
            "description": "<p>User's first name (provided by the Facebook javascript API).</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "last_name",
            "description": "<p>User's last name (provided by the Facebook javascript API).</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "email",
            "description": "<p>User's email (provided by the Facebook javascript API).</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "gender",
            "description": "<p>User's gender (provided by the Facebook javascript API).</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "click_source",
            "description": "<p>The location of the button (e.g. <code>landing_page_top_right</code>), where the user logged in to Featherq.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "success",
            "description": "<p>Returns <code>1</code> if the sign up is successful.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "accessToken",
            "description": "<p>The access key to remember the session.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"success\": 1,\n    \"accessToken\": \"123123drink123123drink\"\n}",
          "type": "Json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error": [
          {
            "group": "Error",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "success",
            "description": "<p>Returns <code>0</code> if registration fails.</p> "
          },
          {
            "group": "Error",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "AuthenticationFailed",
            "description": "<p>Invalid <code>accessToken</code> or <code>fb_id</code>.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Error-response:",
          "content": "HTTP/1.1 200 OK\n{\n    \"success\": 0,\n    \"err_code\": \"AuthenticationFailed\"\n}",
          "type": "Json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AuthenticationController.php",
    "groupTitle": "Authentication"
  },
  {
    "type": "get",
    "url": "advertisement/{business_id}",
    "title": "Fetch Business Image Ads",
    "name": "FetchAdvertisementImage",
    "group": "Broadcast",
    "version": "1.0.0",
    "examples": [
      {
        "title": "Example Usage:",
        "content": "https://api.featherq.com/advertisement/1",
        "type": "js"
      }
    ],
    "description": "<p>Gets all the image advertisements that have been uploaded by the business.</p> ",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>The unique access key sent by the client.</p> "
          }
        ]
      }
    },
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "business_id",
            "description": "<p>The id of the business.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "<p>Object[]</p> ",
            "optional": false,
            "field": "ad_images",
            "description": "<p>The array of images found on the broadcast screen.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "ad_images.img_id",
            "description": "<p>The id of the image.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "ad_images.path",
            "description": "<p>The filesystem path of the image.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "ad_images.weight",
            "description": "<p>The weight/place of the image.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "ad_images.business_id",
            "description": "<p>The id of the business to which the image belongs.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n[\n  {\n    \"img_id\": 72,\n    \"path\": \"ads\\/125\\/o_1a2pute0r17ns1fi91p8q1vj6ric.jpg\",\n    \"weight\": 19,\n    \"business_id\": 125\n  },\n  {\n    \"img_id\": 74,\n    \"path\": \"ads\\/125\\/o_1a2pute0rmt3nm7f5o10927tue.png\",\n    \"weight\": 21,\n    \"business_id\": 125\n  }\n]",
          "type": "Json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error": [
          {
            "group": "Error",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "NoImagesFound",
            "description": "<p>No images were found using the <code>business_id</code>.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"err_code\": \"NoImagesFound\"\n}",
          "type": "Json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/AdvertisementController.php",
    "groupTitle": "Broadcast"
  },
  {
    "type": "get",
    "url": "broadcast/{raw_code}",
    "title": "Fetch Business Broadcast Data",
    "name": "FetchBroadcastDetails",
    "group": "Broadcast",
    "version": "1.0.0",
    "examples": [
      {
        "title": "Example Usage:",
        "content": "https://api.featherq.com/broadcast/pg21\nhttps://api.featherq.com/broadcast/reminisense-corp",
        "type": "js"
      }
    ],
    "description": "<p>Gets all the data needed to make the broadcast page functional.</p> ",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>The unique access key sent by the client.</p> "
          }
        ]
      }
    },
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "raw_code",
            "description": "<p>The 4 digit code or the personalized url of the business.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "business_id",
            "description": "<p>The id of the business which owns the broadcast screen.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "adspace_size",
            "description": "<p>The space size of the advertisement image.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "numspace_size",
            "description": "<p>The space size of the broadcast numbers.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "box_num",
            "description": "<p>The number of broadcast numbers to show on the screen.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "get_num",
            "description": "<p>The available number for remote queuing.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "display",
            "description": "<p>The display type code.</p> "
          },
          {
            "group": "200",
            "type": "<p>Boolean</p> ",
            "optional": false,
            "field": "show_issued",
            "description": "<p>The flag to show only called numbers or also the issued numbers.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "ad_video",
            "description": "<p>The video ad url.</p> "
          },
          {
            "group": "200",
            "type": "<p>Boolean</p> ",
            "optional": false,
            "field": "turn_on_tv",
            "description": "<p>The flag to check if the tv is on.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "tv_channel",
            "description": "<p>The current channel of the tv.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "date",
            "description": "<p>The current date of business operations.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "ticker_message",
            "description": "<p>The first line of ticker message.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "ticker_message2",
            "description": "<p>The second line of ticker message.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "ticker_message3",
            "description": "<p>The third line of ticker message.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "ticker_message4",
            "description": "<p>The fourth line of ticker message.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "ticker_message5",
            "description": "<p>The fifth line of ticker message.</p> "
          },
          {
            "group": "200",
            "type": "<p>String[]</p> ",
            "optional": false,
            "field": "ad_images",
            "description": "<p>An array containing the image advertisements of the business.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "open_hour",
            "description": "<p>The hour that the business opens.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "open_minute",
            "description": "<p>The minute that the business opens.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "open_ampm",
            "description": "<p>The ampm that the business opens.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "close_hour",
            "description": "<p>The hour that the business closes.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "close_minute",
            "description": "<p>The minute that the business closes.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "close_ampm",
            "description": "<p>The ampm that the business closes.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "local_address",
            "description": "<p>The address of the business.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business_name",
            "description": "<p>The name of the business.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "first_service",
            "description": "<p>The default service of the business.</p> "
          },
          {
            "group": "200",
            "type": "<p>String[]</p> ",
            "optional": false,
            "field": "keywords",
            "description": "<p>Some keywords used for broadcast meta data.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"business_id\": 125,\n   \"adspace_size\": \"117px\",\n    \"numspace_size\": \"117px\",\n    \"carousel_delay\": 5000,\n    \"ad_type\": \"carousel\",\n  \"ad_images\": [\n    {\n    \"img_id\": 72,\n    \"path\": \"ads\\/125\\/o_1a2pute0r17ns1fi91p8q1vj6ric.jpg\",\n    \"weight\": 19,\n    \"business_id\": 125\n    },\n    {\n    \"img_id\": 73,\n    \"path\": \"ads\\/125\\/o_1a2pute0r1u9b83k1rj45ii12pvd.jpg\",\n    \"weight\": 20,\n    \"business_id\": 125\n    },\n    {\n    \"img_id\": 74,\n    \"path\": \"ads\\/125\\/o_1a2pute0rmt3nm7f5o10927tue.png\",\n    \"weight\": 21,\n    \"business_id\": 125\n    }\n  ],\n \"box_num\": \"10\",\n  \"get_num\": 32,\n  \"display\": \"1-10\",\n  \"show_issued\": \"true\",\n  \"ad_video\": \"\\\\\\/\\\\\\/www.youtube.com\\\\\\/embed\\\\\\/EMnDdH8fdEc\",\n  \"turn_on_tv\": \"false\",\n  \"tv_channel\": \"\",\n  \"date\": \"111315\",\n  \"ticker_message\": \"Read\",\n  \"ticker_message2\": \"Yes\",\n  \"ticker_message3\": \"Toast\",\n  \"ticker_message4\": \"\",\n  \"ticker_message5\": \"Yum\",\n  \"open_hour\": 3,\n  \"open_minute\": 0,\n  \"open_ampm\": \"AM\",\n  \"close_hour\": 4,\n  \"close_minute\": 0,\n  \"close_ampm\": \"PM\",\n  \"local_address\": \"Disneyland, Hongkong\",\n  \"business_name\": \"Foo Example\",\n  \"first_service\": {\n    \"service_id\": 125,\n    \"code\": \"\",\n    \"name\": \"Foo Example Service\",\n    \"status\": 1,\n    \"time_created\": \"2015-07-22 07:56:43\",\n    \"branch_id\": 125,\n    \"repeat_type\": \"daily\"\n  },\n  \"keywords\": [\n    \"food\",\n    \"beverage\"\n  ]\n}",
          "type": "Json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error": [
          {
            "group": "Error",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "NoBusinessFound",
            "description": "<p>No businesses were found using the <code>raw_code</code>.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"err_code\": \"NoBusinessFound\"\n}",
          "type": "Json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/BroadcastController.php",
    "groupTitle": "Broadcast"
  },
  {
    "type": "get",
    "url": "/business/search",
    "title": "Search Businesses",
    "name": "Search",
    "group": "Business",
    "version": "1.0.0",
    "examples": [
      {
        "title": "Example Usage",
        "content": "http://api.featherq.com/business/search?keyword=&country=&industry=&time_open=&timezone=&limit=&offset",
        "type": "js"
      }
    ],
    "description": "<p>Search for businesses based on the given parameters.</p> ",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>The unique access key sent by the client.</p> "
          }
        ]
      }
    },
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "keyword",
            "description": "<p>The keyword or name used to search for the business.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "country",
            "description": "<p>The country of the business.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "industry",
            "description": "<p>The industry of the business.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "time_open",
            "description": "<p>The time the business opens. (e.g. <code>11:00 AM</code>)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "timezone",
            "description": "<p>The timezone of the business. (e.g. <code>Asia/Singapore</code>)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": true,
            "field": "limit",
            "description": "<p>The maximum number of entries to be retrieved.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": true,
            "field": "offset",
            "description": "<p>The number where the entries retrieved will start.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "<p>Object[]</p> ",
            "optional": false,
            "field": "business",
            "description": "<p>Array of objects with business details.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "business.business_id",
            "description": "<p>The business id of the retrieved business from the database.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.business_name",
            "description": "<p>The name of the business.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.local_address",
            "description": "<p>The address of the business.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.time_open",
            "description": "<p>The time that the business opens.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.time_close",
            "description": "<p>The time that the business closes.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.waiting_time",
            "description": "<p>Indicates how heavy the queue is based on time it takes for the last number in the queue to be called.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "business.last_number_called",
            "description": "<p>The last number called by the business.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "business.next_available_number",
            "description": "<p>The next number that can be placed to the queue.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "business.last_active",
            "description": "<p>The number of days when the business last processed the queue.</p> "
          },
          {
            "group": "200",
            "type": "<p>Boolean</p> ",
            "optional": false,
            "field": "business.card_bool",
            "description": "<p>Indicates if the business is active or not.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    [\n        \"business_id\": 1,\n        \"business_name\": \"Angel's Burger\",\n        \"local_address\": \"Hernan Cortes st. Subangdako, Mandaue City\",\n        \"time_open\": \"10:00 AM\",\n        \"time_close\": \"4:00 PM\",\n        \"waiting_time\": \"light\",\n        \"last_number_called\": \"none\",\n        \"next_available_number\": 1,\n        \"last_active\": 5,\n        \"card_bool\": false\n    ]\n}",
          "type": "Json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/BusinessController.php",
    "groupTitle": "Business"
  },
  {
    "type": "post",
    "url": "business/search",
    "title": "Search Businesses.",
    "name": "Search",
    "group": "Business",
    "version": "1.0.0",
    "examples": [
      {
        "title": "Example Usage:",
        "content": "https://api.featherq.com/business/search",
        "type": "js"
      }
    ],
    "description": "<p>Fetch businesses according to given search parameters.</p> ",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>The unique access key sent by the client.</p> "
          }
        ]
      }
    },
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "keyword",
            "description": "<p>The keyword used to search for the business.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "country",
            "description": "<p>The country of the business.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "industry",
            "description": "<p>The industry of the business.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "time_open",
            "description": "<p>The time the business opens. (e.g. <code>11:00 AM</code>)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "timezone",
            "description": "<p>The timezone of the business. (e.g. <code>Asia/Singapore</code>)</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": true,
            "field": "limit",
            "description": "<p>The maximum number of entries to be retrieved.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": true,
            "field": "offset",
            "description": "<p>The number where the entries retrieved will start.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "<p>Object[]</p> ",
            "optional": false,
            "field": "business",
            "description": "<p>Array of objects with business details.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "business.business_id",
            "description": "<p>The business id of the retrieved business from the database.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.business_name",
            "description": "<p>The name of the business.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.local_address",
            "description": "<p>The address of the business.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.time_open",
            "description": "<p>The time that the business opens.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.time_close",
            "description": "<p>The time that the business closes.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.waiting_time",
            "description": "<p>Indicates how heavy the queue is based on time it takes for the last number in the queue to be called.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "business.last_number_called",
            "description": "<p>The last number called by the business.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "business.next_available_number",
            "description": "<p>The next number that can be placed to the queue.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "business.last_active",
            "description": "<p>The number of days when the business last processed the queue.</p> "
          },
          {
            "group": "200",
            "type": "<p>Boolean</p> ",
            "optional": false,
            "field": "business.card_bool",
            "description": "<p>Indicates if the business is active or not.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "[{\n     \"business_id\": 9,\n     \"business_name\": \"ABCDEF\",\n     \"local_address\": \"Cebu City, Central Visayas, Philippines\",\n     \"time_open\": \"12:00 AM\",\n     \"time_close\": \"8:00 AM\",\n     \"waiting_time\": \"light\",\n     \"last_number_called\": \"none\",\n     \"next_available_number\": 1,\n     \"last_active\": 270.33333333333,\n     \"card_bool\": false\n},\n{\n     \"business_id\": 10,\n     \"business_name\": \"Logitech Gaming\",\n     \"local_address\": \"Cebu City, Central Visayas, Philippines\",\n     \"time_open\": \"8:00 AM\",\n     \"time_close\": \"10:00 PM\",\n     \"waiting_time\": \"light\",\n     \"last_number_called\": \"none\",\n     \"next_available_number\": 1,\n     \"last_active\": 138.33333333333,\n     \"card_bool\": false\n }]",
          "type": "Json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/LandingPageController.php",
    "groupTitle": "Business"
  },
  {
    "type": "get",
    "url": "/business/search-suggest/{keyword}",
    "title": "Search Suggestions",
    "name": "SearchSuggest",
    "group": "Business",
    "version": "1.0.0",
    "examples": [
      {
        "title": "Example Usage",
        "content": "http://api.featherq.com/business/search-suggest/keyword",
        "type": "js"
      }
    ],
    "description": "<p>Suggests search items for businesses based on the given keyword.</p> ",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>The unique access key sent by the client.</p> "
          }
        ]
      }
    },
    "permission": [
      {
        "name": "none"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "keyword",
            "description": "<p>The keyword used to search for the business.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "<p>Object[]</p> ",
            "optional": false,
            "field": "business",
            "description": "<p>Array of objects with business details.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.business_name",
            "description": "<p>The name of the business.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "business.local_address",
            "description": "<p>The address of the business.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n    [\n        \"business_name\": \"Angel's Burger\",\n        \"local_address\": \"Hernan Cortes st. Subangdako, Mandaue City\",\n    ]\n}",
          "type": "Json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/BusinessController.php",
    "groupTitle": "Business"
  },
  {
    "type": "post",
    "url": "queue/insert-specific",
    "title": "Inserts Specific Number",
    "name": "PostInsertSpecific",
    "group": "Queue",
    "version": "1.0.0",
    "examples": [
      {
        "title": "Example Usage:",
        "content": "https://api.featherq.com/queue/insert-specific",
        "type": "js"
      }
    ],
    "description": "<p>This function enables the authorized user to queue by inserting the validated number to the database.</p> ",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>The unique access key sent by the client.</p> "
          }
        ]
      }
    },
    "permission": [
      {
        "name": "Authenticated User"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "service_id",
            "description": "<p>The id of the service to queue.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "terminal_id",
            "description": "<p>The id of the terminal to queue.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "allowedValues": [
              "\"web\"",
              "\"remote\"",
              "\"android\"",
              "\"specific\""
            ],
            "optional": false,
            "field": "queue_platform",
            "description": "<p>The platform where the queue is requested. <code>Web</code> is generated from the web app. <code>Remote</code> is from remote queueing-web app. <code>Android</code> is from remote queueing-Android app. <code>Specific</code> is from the process queue-issue specific number.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "priority_number",
            "description": "<p>The number issued to the user.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "name",
            "description": "<p>The full name of the user that is queuing.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "phone",
            "description": "<p>The contact number of the user that is queuing.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "email",
            "description": "<p>The email address of the user that is queuing.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "date",
            "description": "<p>The timestamp format (<code>mktime(0, 0, 0, date('m'), date('d'), date('Y'))</code>) of the date the queue is requested.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "user_id",
            "description": "<p>The id of the user requesting the queue.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "time_assigned",
            "description": "<p>The time (<code>time()</code>) on which the queue was inserted to the database.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "success",
            "description": "<p>The boolean flag of the successful process.</p> "
          },
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "transaction_number",
            "description": "<p>The id of the current transaction.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "priority_number",
            "description": "<p>The number given to the user.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "confirmation_code",
            "description": "<p>The code given to the user along with the priority number for validation.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n[\n  {\n     \"success\": 1\n  },\n  {\n    \"transaction_number\": 73123122,\n    \"priority_number\": \"21\",\n    \"confirmation_code\": 1GHB3JS987\n  }\n]",
          "type": "Json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error": [
          {
            "group": "Error",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "InvalidTransaction",
            "description": "<p>The transaction is invalid.</p> "
          },
          {
            "group": "Error",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "InvalidMember",
            "description": "<p>The terminal id is not owned by the service.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 200 OK\n[\n  {\n     \"success\": 0\n  },\n  {\n    \"err_code\": \"InvalidTransaction\"\n  },\n]",
          "type": "Json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/QueueController.php",
    "groupTitle": "Queue"
  },
  {
    "type": "get",
    "url": "user/{user_id}",
    "title": "Fetch all User Details",
    "name": "FetchUserProfile",
    "group": "User",
    "version": "1.0.0",
    "examples": [
      {
        "title": "Example Usage:",
        "content": "https://api.featherq.com/user/1",
        "type": "js"
      }
    ],
    "description": "<p>Gets all the information pertaining to the user.</p> ",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>The unique access key sent by the client.</p> "
          }
        ]
      }
    },
    "permission": [
      {
        "name": "Authenticated User"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "user_id",
            "description": "<p>The id of the user.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "user_id",
            "description": "<p>The id of the user.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "email",
            "description": "<p>The email address of the user.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "first_name",
            "description": "<p>The first name of the user.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "last_name",
            "description": "<p>The last name of the user.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "phone",
            "description": "<p>The phone number of the user.</p> "
          },
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "local_address",
            "description": "<p>The address of the user.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"user_id\": \"13\",\n  \"email\": \"foo@example.com\",\n  \"first_name\": \"Foo Foo\",\n  \"last_name\": \"Example\",\n  \"phone\": \"1234567890\",\n  \"local_address\": \"Disneyland, Hongkong\"\n}",
          "type": "Json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error": [
          {
            "group": "Error",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "UserNotFound",
            "description": "<p>There were no users found with the given <code>user_id</code>.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"err_code\": \"UserNotFound\"\n}",
          "type": "Json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "put",
    "url": "user/update",
    "title": "Update User Information",
    "name": "UpdateUserInfo",
    "group": "User",
    "version": "1.0.0",
    "examples": [
      {
        "title": "Example Usage:",
        "content": "https://api.featherq.com/user/update",
        "type": "js"
      }
    ],
    "description": "<p>Update user information using the information given from JSON.</p> ",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "access-key",
            "description": "<p>The unique access key sent by the client.</p> "
          }
        ]
      }
    },
    "permission": [
      {
        "name": "Authenticated User"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "user_id",
            "description": "<p>The id of the user.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "first_name",
            "description": "<p>The modified first name of user.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "last_name",
            "description": "<p>The modified last name of user.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "phone",
            "description": "<p>The modified contact number of user.</p> "
          },
          {
            "group": "Parameter",
            "type": "<p>String</p> ",
            "optional": true,
            "field": "local_address",
            "description": "<p>The modified address of user.</p> "
          }
        ]
      }
    },
    "success": {
      "fields": {
        "200": [
          {
            "group": "200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "success",
            "description": "<p>The flag indicating the success/failure of update process. Returns <code>1</code> if process was successful.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n     \"success\" : 1\n }",
          "type": "Json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error": [
          {
            "group": "Error",
            "type": "<p>Number</p> ",
            "optional": false,
            "field": "success",
            "description": "<p>The flag indicating the success/failure of update process. Returns <code>0</code> if process was not successful.</p> "
          },
          {
            "group": "Error",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "UserNotFound",
            "description": "<p>There were no users found with the given <code>user_id</code>.</p> "
          },
          {
            "group": "Error",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "SomethingWentWrong",
            "description": "<p>Something went wrong while saving your data.</p> "
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 200 OK\n{\n     \"success\": \"0\",\n     \"err_code\": \"UserNotFound\"\n}",
          "type": "Json"
        }
      ]
    },
    "filename": "./app/Http/Controllers/UserController.php",
    "groupTitle": "User"
  },
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p> "
          },
          {
            "group": "Success 200",
            "type": "<p>String</p> ",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p> "
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./doc/main.js",
    "group": "c__wamp_www_feaq_api_doc_main_js",
    "groupTitle": "c__wamp_www_feaq_api_doc_main_js",
    "name": ""
  }
] });