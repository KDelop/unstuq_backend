define({ "api": [
  {
    "type": "get",
    "url": "/get_date_ideas",
    "title": "Date Idea Data",
    "name": "Get_Date_Idea",
    "group": "DateIdea",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n{\n      \"status\": true,\n      \"data\":[\n        {\n         \"id\": 10,\n         \"title\": \"Hike at sunrise/sunset\",\n         \"image\": \"\",\n         \"likes\": 1,\n         \"user_like_status\": 1\n         },\n         {\n          \"id\": 7,\n          \"title\": \"Play racquetball or tennis\",\n          \"image\": \"\",\n          \"likes\": 0,\n          \"user_like_status\": 0\n         }\n    ]\n      \"message\": \"Date Idea List\"\n }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/DateIdeaController.php",
    "groupTitle": "DateIdea",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/get_date_ideas"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get_date_idea_details/{id}",
    "title": "Get Date Idea Details",
    "name": "Get_Date_Idea_Details",
    "group": "DateIdea",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n{\n      \"status\": true,\n      \"data\":{\n           \"id\": 3,\n           \"title\": \"Break a sweat jumping trampoline\",\n           \"description\": \"\\\"Visit an extreme trampoline park. It is a lot of fun and a great way to show off\\\"\",\n           \"image\": \"\",\n           \"likes\": 6,\n           \"instructions\": \"\",\n           \"submitted_by\": \"\",\n           \"created_at\": \"2020-09-29 10:39:04\",\n           \"category\": \"\",\n           \"difficulty\": 0,\n           \"user_like_status\": 1\n    }\n      \"message\": \"Date Idea Details\"\n }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/DateIdeaController.php",
    "groupTitle": "DateIdea",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/get_date_idea_details/{id}"
      }
    ]
  },
  {
    "type": "post",
    "url": "/save_date_idea_likes",
    "title": "Store Date Idea Like",
    "name": "Store_Date_Idea_Likes",
    "group": "DateIdea",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "date_idea_id",
            "description": "<p>data idea id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "user_id",
            "description": "<p>user id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>status 1/0.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n{\n      \"status\": true,\n      \"message\": \"Date Idea Like Successfully Added\"\n }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Not Found\n{\n   \"status\": false,\n   \"message\": \"The date idea id field is required., The user id field is required., The status field is required.\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/DateIdeaController.php",
    "groupTitle": "DateIdea",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/save_date_idea_likes"
      }
    ]
  },
  {
    "type": "post",
    "url": "/login",
    "title": "User Login",
    "name": "User_Login",
    "group": "LoginRegister",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": "<p>User Phone.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"message\": \"Access code send to your email/phone no.\",\n     \"user_token\" : \"temparary token for verification api\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Not Found\n{\n   \"status\": false,\n   \"message\": \"no email or phone provided\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/ApiController.php",
    "groupTitle": "LoginRegister",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/login"
      }
    ]
  },
  {
    "type": "post",
    "url": "/register",
    "title": "User Register",
    "name": "User_Register",
    "group": "LoginRegister",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Mandatory User Name.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>Mandatory User Email.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": "<p>Mandatory User Phone.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "countryCode",
            "description": "<p>Mandatory User Country code.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"message\": \"Account created successfully.\"\n     \"user_token\" : \"temparary token for verification api\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 200 Not Found\n{\n   \"status\": false,\n   \"message\": \"An account with this email or phone number already exists. Please login instead.\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Not Found\n{\n   \"status\": false,\n   \"message\": \"The email field is required., The name field is required., The phone field is required.\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/ApiController.php",
    "groupTitle": "LoginRegister",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/register"
      }
    ]
  },
  {
    "type": "post",
    "url": "/verify_access_code",
    "title": "User Verify",
    "name": "User_Verify",
    "group": "LoginRegister",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_token",
            "description": "<p>temparary token.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "code",
            "description": "<p>access code.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_uuid",
            "description": "<p>device unique id.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_type",
            "description": "<p>andriod (1) or ios (2) device.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_name",
            "description": "<p>device name.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"message\": \"Access code send to your email/phone no.\",\n     \"token\" : \"Authorization token\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "   HTTP/1.1 200 Not Found\n{\n      \"status\": false,\n      \"message\": \"Invalid code\"\n }",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": "   HTTP/1.1 400 Not Found\n{\n      \"status\": false,\n      \"message\": \"The code field is required., The user token field is required.\",\n }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/ApiController.php",
    "groupTitle": "LoginRegister",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/verify_access_code"
      }
    ]
  },
  {
    "type": "post",
    "url": "/logout",
    "title": "User Logout",
    "name": "verify_user",
    "group": "LoginRegister",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"message\": \"Logout successfully\",\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 200 Not Found\n{\n   \"status\": false,\n   \"message\": \"Invalid Token\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/ApiController.php",
    "groupTitle": "LoginRegister",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/logout"
      }
    ]
  },
  {
    "type": "delete",
    "url": "/search/delete",
    "title": "Delete Search",
    "name": "Delete_search",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "search_id",
            "description": "<p>search id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n  {\n    \"status\": true,\n    \"message\": \"Successfully Deleted\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/search/delete"
      }
    ]
  },
  {
    "type": "get",
    "url": "/search/get",
    "title": "Get Search Results",
    "name": "Get_Search_Results",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "search_id",
            "description": "<p>search_id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": {\n       \"results\": [\n           {\n               \"photo\": \"https://media-cdn.tripadvisor.com/media/photo-o/14/97/93/09/sweet-dream-a-potpourri.jpg\",\n               \"location_string\": \"Pattaya, Chonburi Province\",\n               \"num_reviews\": \"1162\",\n               \"name\": \"Casa Pascal Restaurant\",\n               \"location_id\": \"1130181\",\n               \"longitude\": \"100.87983\",\n               \"latitude\": \"12.928686\",\n               \"cuisine\": \"Thai\",\n               \"address\": \"485/4 Moo 10, Second Road Opposite Royal Garden Plaza, Pattaya 20260 Thailand\",\n               \"write_review\": \"https://www.tripadvisor.com/UserReview-g293919-d1130181-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html\",\n               \"web_url\": \"https://www.tripadvisor.com/Restaurant_Review-g293919-d1130181-Reviews-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html\",\n               \"description\": \"Happy Dining. Setting culinary standards in Pattaya since 2001. In this oasis of Pattaya we cook the food with freshest ingredients, most of them seasonal and from local and imported sources.\",\n               \"price\": \"$10 - $30\",\n               \"rating\": \"4.5\",\n               \"distance_string\": \"1.1 mi\",\n               \"ranking\": \"#24 of 1,300 Restaurants in Pattaya\",\n               \"website\": \"http://www.restaurant-in-pattaya.com/\",\n               \"phone\": \"+66 61 643 9969\"\n           }],\n        \"compulsory_likes\": 5\n    }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/search/get"
      }
    ]
  },
  {
    "type": "get",
    "url": "/search/get",
    "title": "Get Search Results",
    "name": "Get_Search_Results",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "search_id",
            "description": "<p>search_id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": {\n       \"results\": [\n           {\n               \"photo\": \"https://media-cdn.tripadvisor.com/media/photo-o/14/97/93/09/sweet-dream-a-potpourri.jpg\",\n               \"location_string\": \"Pattaya, Chonburi Province\",\n               \"num_reviews\": \"1162\",\n               \"name\": \"Casa Pascal Restaurant\",\n               \"location_id\": \"1130181\",\n               \"longitude\": \"100.87983\",\n               \"latitude\": \"12.928686\",\n               \"cuisine\": \"Thai\",\n               \"address\": \"485/4 Moo 10, Second Road Opposite Royal Garden Plaza, Pattaya 20260 Thailand\",\n               \"write_review\": \"https://www.tripadvisor.com/UserReview-g293919-d1130181-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html\",\n               \"web_url\": \"https://www.tripadvisor.com/Restaurant_Review-g293919-d1130181-Reviews-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html\",\n               \"description\": \"Happy Dining. Setting culinary standards in Pattaya since 2001. In this oasis of Pattaya we cook the food with freshest ingredients, most of them seasonal and from local and imported sources.\",\n               \"price\": \"$10 - $30\",\n               \"rating\": \"4.5\",\n               \"distance_string\": \"1.1 mi\",\n               \"ranking\": \"#24 of 1,300 Restaurants in Pattaya\",\n               \"website\": \"http://www.restaurant-in-pattaya.com/\",\n               \"phone\": \"+66 61 643 9969\"\n           }],\n        \"compulsory_likes\": 5\n    }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/search/get"
      }
    ]
  },
  {
    "type": "get",
    "url": "/location/search_filters",
    "title": "Get all search filters",
    "name": "Get_all_search_filters",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": ''\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/location/search_filters"
      }
    ]
  },
  {
    "name": "Get_genre",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>movie,tv</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search"
  },
  {
    "type": "get",
    "url": "/location/get",
    "title": "Get location details",
    "name": "Get_location_details",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>restaurants,attractions,hotels</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "location_id",
            "description": "<p>unique location id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": {\n     \"location_detail\": {\n           \"location_id\": \"6564765\",\n           \"longitude\": \"100.87312\",\n           \"latitude\": \"12.910579\",\n           \"num_reviews\": \"30\",\n           \"name\": \"Good Farmer Homemade & Hydroponics Farm\",\n           \"description\": \"A fine cozy restaurant in a garden and hydroponics salad farm, the one and only in Pattaya. We serve variety of Breakfast, All Day Delicious International and Thai-tasted menu such as fresh hash-brown breakfast, salmon steak, tom-yum-kung, gang-kiew-wan and homemade bakery by our Big Sister. All delicious menus serve with fresh-cut salads from our own farm, Good Farmer Hydroponics. See You Soon^^\",\n           \"price\": \"$50 - $280\",\n           \"rating\": \"4.5\",\n           \"ranking\": \"#205 of 1,445 places to eat in Pattaya\",\n           \"phone\": \"+66 83 854 9266\",\n           \"address\": \"308/13 Moo 12, Soi Thappaya 15, Thappaya Road, Pattaya 20150 Thailand\",\n           \"reviews\": \"\",\n           \"web_url\": \"https://www.tripadvisor.com/Restaurant_Review-g293919-d6564765-Reviews-Good_Farmer_Homemade_Hydroponics_Farm-Pattaya_Chonburi_Province.html\",\n           \"cuisine\": \"Vegetarian Friendly\",\n           \"menu_web_url\": \"\",\n           \"email\": \"nin.kanokmanee@gmail.com\",\n           \"website\": \"http://www.facebook.com/GoodFarmerHomemade/\",\n           \"owners_top_reasons\": \"\",\n           \"photo_count\": \"28\",\n           \"photo\": \"https://media-cdn.tripadvisor.com/media/photo-w/06/85/17/8b/himmapan.jpg\",\n           \"location_string\": \"Pattaya, Chonburi Province\"\n       },\n     \"photos\": {\n           \"photos\": [\n               {\n                   \"photo\": \"https://media-cdn.tripadvisor.com/media/photo-s/06/85/17/8f/himmapan.jpg\",\n                   \"caption\": \"We serve good Thai and International foods, wow taste with fresh own-grown Good Farmer Salad.\",\n                   \"helpful_votes\": \"1\",\n                   \"published_date\": \"2014-09-15T03:12:23-0400\"\n               }\n           ]\n      }\n    }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/location/get"
      }
    ]
  },
  {
    "type": "get",
    "url": "/location/get_tips",
    "title": "Get location tips",
    "name": "Get_location_tips",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>restaurants,attractions,hotels</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "location_id",
            "description": "<p>unique location id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": {\n        \"tips\": [\n           {\n               \"username\": \"billyv419\",\n               \"type\": \"room_tip\",\n               \"text\": \"Room in the corner has the 360 sea viewi\",\n               \"rating\": \"4\"\n           }\n         ]\n    }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/location/get_tips"
      }
    ]
  },
  {
    "type": "get",
    "url": "/movie/get",
    "title": "Get movie| tv details",
    "name": "Get_movie_or_TV_details",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>movie,tv</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>movie id, tv id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/movie/get"
      }
    ]
  },
  {
    "type": "get",
    "url": "/location/search_matched_pending",
    "title": "Get pending and matched",
    "name": "Get_pending_and_matched",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": {\n                   'pending' => [],\n                   'matched' => []\n    }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/location/search_matched_pending"
      }
    ]
  },
  {
    "name": "Get_streaming_provider",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": []\n}",
          "type": "json"
        }
      ]
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search"
  },
  {
    "type": "post",
    "url": "/location/like_dislike",
    "title": "Like dislike location",
    "name": "Like_dislike_location",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "search_id",
            "description": "<p>search_id</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "location_ids",
            "description": "<p>location_id json string : [    {       &quot;location_id&quot;:13388091,       &quot;like_dislike&quot;:1    } ]</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": ''\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/location/like_dislike"
      }
    ]
  },
  {
    "type": "get",
    "url": "/location/search",
    "title": "Search location",
    "name": "Search_location",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>restaurants,attractions,hotels</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "location_name",
            "description": "<p>location name</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "latitude",
            "description": "<p>current location latitude</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "longitude",
            "description": "<p>current location longitude</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "group_id",
            "description": "<p>selected group id (pass zero for solo)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "deadline",
            "description": "<p>required for group search</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "prices_restaurants",
            "description": "<p>comma separated values of price for restaurants</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "combined_food",
            "description": "<p>comma separated values of cuisine types for restaurants</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "attraction_category",
            "description": "<p>category filter for attractions</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "search_day",
            "description": "<p>user meet/event date 2012-05-23</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "search_time",
            "description": "<p>user meet/event time 22:00</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "search_title",
            "description": "<p>search title</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "offset",
            "description": "<p>for pagination default - 0</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "lunit",
            "description": "<p>unit of distance - mi or km ( default : mi )</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "distance",
            "description": "<p>search raduis( default : 5 )</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": {\n        \"search_id\": 8,\n        \"deadline\":\"2020-07-28 02:00:00\",\n        \"search_results\": {\n           \"result_count\": 5,\n           \"results\": [\n               {\n                   \"photo\": \"https://media-cdn.tripadvisor.com/media/photo-o/14/97/93/09/sweet-dream-a-potpourri.jpg\",\n                   \"location_string\": \"Pattaya, Chonburi Province\",\n                   \"num_reviews\": \"1162\",\n                   \"name\": \"Casa Pascal Restaurant\",\n                   \"location_id\": \"1130181\",\n                   \"longitude\": \"100.87983\",\n                   \"latitude\": \"12.928686\",\n                   \"cuisine\": \"Thai\",\n                   \"address\": \"485/4 Moo 10, Second Road Opposite Royal Garden Plaza, Pattaya 20260 Thailand\",\n                   \"write_review\": \"https://www.tripadvisor.com/UserReview-g293919-d1130181-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html\",\n                   \"web_url\": \"https://www.tripadvisor.com/Restaurant_Review-g293919-d1130181-Reviews-Casa_Pascal_Restaurant-Pattaya_Chonburi_Province.html\",\n                   \"description\": \"Happy Dining. Setting culinary standards in Pattaya since 2001. In this oasis of Pattaya we cook the food with freshest ingredients, most of them seasonal and from local and imported sources.\",\n                   \"price\": \"$10 - $30\",\n                   \"rating\": \"4.5\",\n                   \"distance_string\": \"1.1 mi\",\n                   \"ranking\": \"#24 of 1,300 Restaurants in Pattaya\",\n                   \"website\": \"http://www.restaurant-in-pattaya.com/\",\n                   \"phone\": \"+66 61 643 9969\"\n               }],\n             \"offset\": 0\n          },\n          \"compulsory_likes\": 5\n   }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/location/search"
      }
    ]
  },
  {
    "type": "get",
    "url": "/search",
    "title": "Search movie or tv show",
    "name": "Search_movie_or_tv_show",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "group_id",
            "description": "<p>selected group id (pass zero for solo)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "genre",
            "description": "<p>comma separated genre ids</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "network",
            "description": "<p>networks id</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>movie,tv</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "deadline",
            "description": "<p>required for group search</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "search_day",
            "description": "<p>user meet/event date 2012-05-23</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "search_time",
            "description": "<p>user meet/event time 22:00</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "search_title",
            "description": "<p>search title</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "description": "<p>page no</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": {\n       \"page\": 1,\n       \"total\": 10000,\n       \"total_pages\": 500,\n       \"results\": [\n           {\n               \"image\": \"/5myQbDzw3l8K9yofUXRJ4UTVgam.jpg\",\n               \"id\": 429617,\n               \"genres\": [\n                   \"Drama\",\n                   \"Action & Adventure\",\n                   \"Sci-Fi & Fantasy\"\n               ],\n               \"title\": \"Spider-Man: Far from Home\",\n               \"popularity\": 86.042,\n               \"vote_average\": 7.5,\n               \"vote_count\": 7529,\n               \"overview\": \"Peter Parker and his friends go on a summer trip to Europe. However, they will hardly be able to rest - Peter will have to agree to help Nick Fury uncover the mystery of creatures that cause natural disasters and destruction throughout the continent.\",\n               \"release_date\": \"2019-06-28\"\n           }...]\n      }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/search"
      }
    ]
  },
  {
    "type": "post",
    "url": "/search/save_solo_match",
    "title": "Solo save matched",
    "name": "Solo_save_matched",
    "group": "Search",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Numeric",
            "optional": false,
            "field": "search_id",
            "description": "<p>unique search identifier</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "matched_location_id",
            "description": "<p>matched_location_id for search</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n   \"status\": \"1\",\n   \"data\": ''\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/SearchController.php",
    "groupTitle": "Search",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/search/save_solo_match"
      }
    ]
  },
  {
    "type": "post",
    "url": "/user/favorite/add",
    "title": "Add Favorite",
    "name": "Add_Favorite",
    "group": "User",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>favortie location  type</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "entity_id",
            "description": "<p>favortie location id</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "location_name",
            "description": "<p>location_name</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "address",
            "description": "<p>location address id</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "longitude",
            "description": "<p>location longitude</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "latitude",
            "description": "<p>location latitude</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "rating",
            "description": "<p>location rating</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "ranking",
            "description": "<p>location ranking</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "info",
            "description": "<p>location info</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "image",
            "description": "<p>location image url</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n  {\n    \"status\": true,\n    \"message\": \"Successfully added favortie\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/UserController.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/user/favorite/add"
      }
    ]
  },
  {
    "type": "post",
    "url": "/user/feedback/add",
    "title": "Add Feedback",
    "name": "Add_Feedback",
    "group": "User",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "category",
            "description": "<p>1 for bug,2 for enhancement</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n  {\n    \"status\": true,\n    \"message\": \"Successfully Added feedback\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/UserController.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/user/feedback/add"
      }
    ]
  },
  {
    "type": "post",
    "url": "/user/skip/add",
    "title": "Add skip user",
    "name": "Add_skip_user",
    "group": "User",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "search_transaction_id",
            "description": "<p>search transaction id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n  {\n    \"status\": true,\n    \"message\" => 'Successfully added skipped user'\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/UserController.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/user/skip/add"
      }
    ]
  },
  {
    "type": "delete",
    "url": "/user/favorite/delete",
    "title": "Delete Favorites",
    "name": "Delete_Favorite",
    "group": "User",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "entity_ids",
            "description": "<p>entity ids : 110311,2090808</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "type",
            "description": "<p>1: restaurants, 2:attractions, 3:hotels, 4:movie | tv</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n  {\n    \"status\": true,\n    \"message\": \"Successfully Deleted\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/UserController.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/user/favorite/delete"
      }
    ]
  },
  {
    "type": "get",
    "url": "/get-countries",
    "title": "Get Countries",
    "name": "Get_Countries",
    "group": "User",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n  {\n    \"status\": true,\n    \"data\": {\n         \"countries\" => []\n     }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/ApiController.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/get-countries"
      }
    ]
  },
  {
    "type": "get",
    "url": "/user/favorite/get",
    "title": "Get Favorites",
    "name": "Get_Favorite",
    "group": "User",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "type",
            "description": "<p>1: restaurants, 2:attractions, 3:hotels, 4:movie | tv</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "  HTTP/1.1 200 OK\n{\n  \"status\": true,\n  \"data\": {\n     \"favorties\" : []\n   }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/UserController.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/user/favorite/get"
      }
    ]
  },
  {
    "type": "get",
    "url": "/user/feedback/get",
    "title": "Get Feedback",
    "name": "Get_Feedback",
    "group": "User",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n  {\n    \"status\": true,\n    \"data\": {\n         \"feedbacks\" => []\n     }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/UserController.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/user/feedback/get"
      }
    ]
  },
  {
    "type": "get",
    "url": "/user/profile/get",
    "title": "Get Profile Details",
    "name": "Get_Profile_Details",
    "group": "User",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "  HTTP/1.1 200 OK\n{\n  \"status\": true,\n  \"data\": {\n     \"id\": 1,\n      \"name\": \"neha bhole\",\n      \"avatar\": \"storage/user/IMG_1.jpg\",\n      \"email\": \"neha.bhole2008@gmail.com\",\n      \"phone\": \"+911234567890\",\n      \"status\": 1,\n      \"created_at\": \"2020-06-10 09:33:28\"\n   }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/UserController.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/user/profile/get"
      }
    ]
  },
  {
    "type": "post",
    "url": "/group/create",
    "title": "Create User Group",
    "name": "Create_User_Group",
    "group": "UserGroup",
    "description": "<p>Note : user body/form-data paramter option for this api otherwise file will not be uploaded.</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "group_name",
            "description": "<p>group name.</p>"
          },
          {
            "group": "Parameter",
            "type": "File",
            "optional": true,
            "field": "group_icon",
            "description": "<p>group icon.</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"message\": \"Successfully created group\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/GroupController.php",
    "groupTitle": "UserGroup",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/group/create"
      }
    ]
  },
  {
    "type": "delete",
    "url": "/group/delete",
    "title": "Delete User Group",
    "name": "Delete_User_Group",
    "group": "UserGroup",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "group_id",
            "description": "<p>group id.</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"message\": \"Successfully updated group\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/GroupController.php",
    "groupTitle": "UserGroup",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/group/delete"
      }
    ]
  },
  {
    "type": "get",
    "url": "/group/all",
    "title": "Get All Groups",
    "name": "Get_Groups",
    "group": "UserGroup",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": \"1\",\n     \"data\": [\n        {\n           \"id\": 1,\n           \"name\": \"test group\",\n           \"icon\": \"uploads/group/GIMG_1_1870712292.jpeg\",\n           \"created_by\": 1,\n           \"created_at\": \"2020-06-17 15:24:28\",\n           \"members_count\": 1\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/GroupController.php",
    "groupTitle": "UserGroup",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/group/all"
      }
    ]
  },
  {
    "type": "get",
    "url": "/crew/member/get",
    "title": "Get  Crew Member",
    "name": "Get_crew_Member",
    "group": "UserGroup",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "crew_member_id",
            "description": "<p>crew member id.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "group_id",
            "description": "<p>group id.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": \"1\",\n     \"data\":[\n       {\n           \"id\": 1,\n           \"name\": \"test 123\",\n           \"avatar\": \"uploads/user/IMG_1_1218437384.jpg\",\n           \"email\": \"neha.bhole2008@gmail.com\",\n           \"phone\": \"+918879676620\",\n           \"status\": \"active\",\n           \"created_at\": \"2020-06-12 10:04:28\",\n           'pending' => [],\n           'matched' => []\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/GroupController.php",
    "groupTitle": "UserGroup",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/crew/member/get"
      }
    ]
  },
  {
    "type": "post",
    "url": "/group/member/add",
    "title": "Add Group Member",
    "name": "Add_Group_Member",
    "group": "UserGroupMember",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "group_id",
            "description": "<p>group id.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_name",
            "description": "<p>group member name.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_phone_no",
            "description": "<p>group member contact.</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"message\": \"Member added successfully\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/GroupController.php",
    "groupTitle": "UserGroupMember",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/group/member/add"
      }
    ]
  },
  {
    "type": "post",
    "url": "/group/member/add_multiple",
    "title": "Add Group Multiple Members",
    "name": "Add_Group_Members",
    "group": "UserGroupMember",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "group_id",
            "description": "<p>group id.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "members",
            "description": "<p>group member array [ 'user_name' =&gt; 'sadasd' , 'user_phone_no' =&gt; 'dsfdsf'  ].</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"message\": \"Member added successfully\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/GroupController.php",
    "groupTitle": "UserGroupMember",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/group/member/add_multiple"
      }
    ]
  },
  {
    "type": "delete",
    "url": "/group/member/delete",
    "title": "Delete Group Member",
    "name": "Delete_Group_Member",
    "group": "UserGroupMember",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "group_id",
            "description": "<p>group id.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_id",
            "description": "<p>group member.</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"message\": \"Member removed successfully\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/GroupController.php",
    "groupTitle": "UserGroupMember",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/group/member/delete"
      }
    ]
  },
  {
    "type": "post",
    "url": "/group/exit",
    "title": "Exit User Group",
    "name": "Exit_User_Group",
    "group": "UserGroupMember",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "group_id",
            "description": "<p>group id.</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"message\": \"Successfully exit group\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/GroupController.php",
    "groupTitle": "UserGroupMember",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/group/exit"
      }
    ]
  },
  {
    "type": "get",
    "url": "/group/member/all",
    "title": "Get All Group Members",
    "name": "Get_All_Group_Members",
    "group": "UserGroupMember",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "group_id",
            "description": "<p>group id.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": \"1\",\n     \"data\":[\n       {\n           \"id\": 1,\n           \"name\": \"test 123\",\n           \"avatar\": \"uploads/user/IMG_1_1218437384.jpg\",\n           \"email\": \"neha.bhole2008@gmail.com\",\n           \"phone\": \"+918879676620\",\n           \"status\": \"active\",\n           \"created_at\": \"2020-06-12 10:04:28\"\n       }\n   ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/GroupController.php",
    "groupTitle": "UserGroupMember",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/group/member/all"
      }
    ]
  },
  {
    "type": "post",
    "url": "/group/update",
    "title": "Update User Group",
    "name": "Update_User_Group",
    "group": "UserGroup",
    "description": "<p>Note : user body/form-data paramter option for this api otherwise file will not be uploaded.</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "group_id",
            "description": "<p>group id.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "group_name",
            "description": "<p>group name.</p>"
          },
          {
            "group": "Parameter",
            "type": "File",
            "optional": true,
            "field": "group_icon",
            "description": "<p>group icon.</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"message\": \"Successfully updated group\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/GroupController.php",
    "groupTitle": "UserGroup",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/group/update"
      }
    ]
  },
  {
    "type": "post",
    "url": "/user/profile/update",
    "title": "Update Profile Details",
    "name": "Update_Profile_Details",
    "group": "User",
    "description": "<p>Note : user body/form-data paramter option for this api otherwise file will not be uploaded.</p>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>user name</p>"
          },
          {
            "group": "Parameter",
            "type": "File",
            "optional": true,
            "field": "image",
            "description": "<p>Form-based Image Upload</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n  {\n    \"status\": true,\n    \"message\": \"Successfully Updated\",\n    \"data\": {\n        \"id\": 1,\n        \"name\": \"Neha bhole\",\n        \"avatar\": \"storage/user/IMG_1_1907101508.png\",\n        \"email\": \"neha.bhole2008@gmail.com\",\n        \"phone\": \"+918879676620\",\n        \"status\": \"active\",\n        \"created_at\": \"2020-06-12 10:04:28\"\n     }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/UserController.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/user/profile/update"
      }
    ]
  },
  {
    "type": "get",
    "url": "/home",
    "title": "User Home page",
    "name": "home_page",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "location_lat",
            "description": "<p>user lat location.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "location_long",
            "description": "<p>user long location.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "player_id",
            "description": "<p>user player id.</p>"
          }
        ]
      }
    },
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "Authorization",
            "defaultValue": "bearer bd970a05-0ec1-4412-8b28-657962f0f778",
            "description": ""
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "   HTTP/1.1 200 OK\n{\n     \"status\": true,\n     \"data\": {\n          \"restaurants\" : [],\n          \"attractions\" : []\n      }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 200 Not Found\n{\n   \"status\": false,\n   \"message\": \"lat long not provided\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": " HTTP/1.1 200 Not Found\n{\n   \"status\": false,\n    \"message\": \"Authorization Token not found\"\n }",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": " HTTP/1.1 200 Not Found\n{\n   \"status\": false,\n    \"message\": \"Token is Invalid\"\n }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/Api/UserController.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "https://devapi.unstuq.com/api/v1/home"
      }
    ]
  }
] });
