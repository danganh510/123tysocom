var LValidator={validEmail:function(t){return/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(t)},validTel:function(t){return/^\+?\d+$/gm.test(t)},validNumber:function(t){return/^\+?\d+(\.\d+)?$/gm.test(t)},validInt:function(t){return/^\+?\d+$/gm.test(t)},validDomain:function(t){return/^[^-]([A-Za-z0-9-]{1,63}[^-]\.)+[A-Za-z]{2,6}$/gm.test(t)}};