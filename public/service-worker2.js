/**
 * Copyright 2018 Google Inc. All Rights Reserved.
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// If the loader is already loaded, just stop.
if (!self.define) {
  const singleRequire = name => {
    if (name !== 'require') {
      name = name + '.js';
    }
    let promise = Promise.resolve();
    if (!registry[name]) {
      
        promise = new Promise(async resolve => {
          if ("document" in self) {
            const script = document.createElement("script");
            script.src = name;
            document.head.appendChild(script);
            script.onload = resolve;
          } else {
            importScripts(name);
            resolve();
          }
        });
      
    }
    return promise.then(() => {
      if (!registry[name]) {
        throw new Error(`Module ${name} didn’t register its module`);
      }
      return registry[name];
    });
  };

  const require = (names, resolve) => {
    Promise.all(names.map(singleRequire))
      .then(modules => resolve(modules.length === 1 ? modules[0] : modules));
  };
  
  const registry = {
    require: Promise.resolve(require)
  };

  self.define = (moduleName, depsNames, factory) => {
    if (registry[moduleName]) {
      // Module is already loading or loaded.
      return;
    }
    registry[moduleName] = Promise.resolve().then(() => {
      let exports = {};
      const module = {
        uri: location.origin + moduleName.slice(1)
      };
      return Promise.all(
        depsNames.map(depName => {
          switch(depName) {
            case "exports":
              return exports;
            case "module":
              return module;
            default:
              return singleRequire(depName);
          }
        })
      ).then(deps => {
        const facValue = factory(...deps);
        if(!exports.default) {
          exports.default = facValue;
        }
        return exports;
      });
    });
  };
}
define("./service-worker2.js",['./workbox-f31c35c4'], function (workbox) { 'use strict';

  /**
  * Welcome to your Workbox-powered service worker!
  *
  * You'll need to register this file in your web app.
  * See https://goo.gl/nhQhGp
  *
  * The rest of the code is auto-generated. Please don't update this file
  * directly; instead, make changes to your Workbox build configuration
  * and re-run your build process.
  * See https://goo.gl/2aRDsh
  */

  workbox.skipWaiting();
  workbox.clientsClaim();
  /**
   * The precacheAndRoute() method efficiently caches and responds to
   * requests for URLs in the manifest.
   * See https://goo.gl/S9QRab
   */

  workbox.precacheAndRoute([{
    "url": "/build/ajax.js",
    "revision": "2478572ab5f279962473a390eb144ed6"
  }, {
    "url": "/build/app.js",
    "revision": "d00464dd9e89f3ec9d043c1d3404795e"
  }, {
    "url": "/build/login.js",
    "revision": "364880e52bb018a209f36e7dc1abb822"
  }, {
    "url": "/build/main.css",
    "revision": "82d006893f6d9f43efa085a36a6d12c7"
  }, {
    "url": "/build/mercure.js",
    "revision": "717230e0ca58520a88f9906b6003ccb9"
  }, {
    "url": "/build/runtime.js",
    "revision": "000c3893cd42406aebf5454f3954e3e5"
  }, {
    "url": "/build/vendors~ajax.js",
    "revision": "1e6ebb5cce99dc158b36d774a8953901"
  }, {
    "url": "/build/vendors~ajax~login.js",
    "revision": "fedbb7d8547b5ff6cfe7de3108a358cc"
  }, {
    "url": "/build/vendors~app.js",
    "revision": "3c5c40535e9cb63e04609a876360219c"
  }, {
    "url": "/build/vendors~login.js",
    "revision": "f940d2adb3635c8d9a7be0687f967fc1"
  }, {
    "url": "/build/vendors~main.css",
    "revision": "07aed0ccb7fd3c1ecad4a8bf86c01a75"
  }, {
    "url": "/build/vendors~main.js",
    "revision": "4d1cfb6e2d7505a65da864536fa35018"
  }], {});
  workbox.registerRoute(/^(?!https:\/\/s-website\.ga\/\.well-known\/mercure\?topic=%2Fmessage).*$/, new workbox.StaleWhileRevalidate(), 'GET');

});
//# sourceMappingURL=service-worker2.js.map