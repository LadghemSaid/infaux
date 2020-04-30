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
define("./service-worker.js",['./workbox-f31c35c4'], function (workbox) { 'use strict';

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
    "revision": "58974c0aa4b8cdf82eb0cf7cd9374394"
  }, {
    "url": "/build/app.js",
    "revision": "76fbad32d71b9f34786c8792699a5ded"
  }, {
    "url": "/build/login.js",
    "revision": "aa8f52265eba7efd2af79c8c6313bfb3"
  }, {
    "url": "/build/main.css",
    "revision": "7020150f7695f920e80f3d4137422dde"
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
    "revision": "755a0156599f02315d1d4f0498613260"
  }, {
    "url": "/build/vendors~main.js",
    "revision": "4d1cfb6e2d7505a65da864536fa35018"
  }], {});
  workbox.registerRoute(/^(?!https:\/\/s-website\.ga\/\.well-known\/mercure\?topic=%2Fmessage).*$/, new workbox.StaleWhileRevalidate(), 'GET');

});
//# sourceMappingURL=service-worker.js.map
