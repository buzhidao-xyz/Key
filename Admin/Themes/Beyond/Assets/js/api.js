define([], function (){
    return {
        host: "/html5js/",
        user: {
            userlist: {
                m: "get",
                u: "api/userlist.json"
            },
            userprofile: {
                m: "get",
                u: "api/userprofile.json"
            }
        },
        goods: {
            goodslist: {
                m: "get",
                u: "api/goodslist.json"
            }
        }
    }
});