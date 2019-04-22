(function(root, factory) {
    "use strict";
    if (typeof define === "function" && define.amd) {
        define(["jquery", "iscroll-zoom", "hammer", "lrz"], factory);
    } else if (typeof exports === "object") {
        module.exports = factory(require("jquery"), require("iscroll-zoom"), require("hammer"), require("lrz"));
    } else {
        root.bjj = root.bjj || {};
        root.bjj.PhotoClip = factory(root.jQuery, root.IScroll, root.Hammer, root.lrz);
    }
} (this,
function($, IScroll, Hammer, lrz) {
    "use strict";
    var defaultOption = {
        size: [260, 260],
        outputSize: [0, 0],
        file: "",
        view: "",
        ok: "",
        loadStart: function() {},
        loadComplete: function() {},
        loadError: function() {},
        clipFinish: function() {}
    }
    function PhotoClip(container, option) {
        if (!window.FileReader) {
            alert("您的浏览器不支持 HTML5 的 FileReader API， 因此无法初始化图片裁剪插件，请更换最新的浏览器！");
            return;
        }
        var opt = $.extend({},
        defaultOption, option);
        var returnValue = photoClip(container, opt);
        this.destroy = returnValue.destroy;
    }
    function photoClip(container, option) {
        var size = option.size,
        outputSize = option.outputSize,
        file = option.file,
        view = option.view,
        ok = option.ok,
        outputType = "image/jpeg",
        loadStart = option.loadStart,
        loadComplete = option.loadComplete,
        loadError = option.loadError,
        clipFinish = option.clipFinish;
        if (!isArray(size)) {
            size = [260, 260];
        }
        if (!isArray(outputSize)) {
            outputSize = [0, 0];
        }
        var clipWidth = size[0] || 260,
        clipHeight = size[1] || 260,
        outputWidth = Math.max(outputSize[0], 0),
        outputHeight = Math.max(outputSize[1], 0);
        var $file = $(file);
        if (!$file.length) return;
        var $img, imgWidth, imgHeight, imgLoaded;
        $file.attr("accept", "image/*");
        $file.on("change",
        function() {
            if (!this.files.length) return;
            var files = this.files[0];
            if (!/image\/\w+/.test(files.type)) {
                alert("图片格式不正确，请选择正确格式的图片文件！");
                return false;
            } else {
                var fileReader = new FileReader();
                fileReader.onprogress = function(e) {
                    console.log((e.loaded / e.total * 100).toFixed() + "%");
                };
                fileReader.onload = function(e) {
                    lrz(files).then(function(rst) {
                        createImg(rst.base64);
                    }).
                    catch(function(err) {
                        alert("图片处理失败");
                        loadError.call(this, err);
                    });
                };
                fileReader.onerror = function(e) {
                    alert("图片加载失败");
                    loadError.call(this, e);
                };
                fileReader.readAsDataURL(files);
                loadStart.call(fileReader, files);
            }
        });
        $file.click(function() {
            this.value = "";
        });
        var $container, $clipView, $moveLayer, $rotateLayer, $view, canvas, hammerManager, myScroll, containerWidth, containerHeight;
        init();
        initScroll();
        initEvent();
        initClip();
        var $ok = $(ok);
        if ($ok.length) {
            $ok.click(function() {
                clipImg();
            });
        }
        var $win = $(window);
        resize();
        $win.resize(resize);
        var atRotation, curX, curY, curAngle;
        function imgLoad() {
            imgLoaded = true;
            $rotateLayer.append(this);
            hideAction.call(this, $img,
            function() {
                imgWidth = this.naturalWidth;
                imgHeight = this.naturalHeight;
            });
            hideAction($moveLayer,
            function() {
                resetScroll();
            });
            loadComplete.call(this, this.src);
        }
        function initScroll() {
            var options = {
                zoom: true,
                scrollX: true,
                scrollY: true,
                freeScroll: true,
                mouseWheel: true,
                wheelAction: "zoom"
            }
            myScroll = new IScroll($clipView[0], options);
        }
        function resetScroll() {
            curX = 0;
            curY = 0;
            curAngle = 0;
            $rotateLayer.css({
                "width": imgWidth,
                "height": imgHeight
            });
            setTransform($rotateLayer, curX, curY, curAngle);
            calculateScale(imgWidth, imgHeight);
            myScroll.zoom(myScroll.options.zoomStart);
            refreshScroll(imgWidth, imgHeight);
            var posX = (clipWidth - imgWidth * myScroll.options.zoomStart) * .5,
            posY = (clipHeight - imgHeight * myScroll.options.zoomStart) * .5;
            myScroll.scrollTo(posX, posY);
        }
        function refreshScroll(width, height) {
            $moveLayer.css({
                "width": width,
                "height": height
            });
            $clipView.append($moveLayer);
            myScroll.refresh();
        }
        function initEvent() {
            var is_mobile = !!navigator.userAgent.match(/mobile/i);
            if (is_mobile) {
                hammerManager = new Hammer($moveLayer[0]);
                hammerManager.add(new Hammer.Rotate());
                var rotation, rotateDirection;
                hammerManager.on("rotatemove",
                function(e) {
                    if (atRotation) return;
                    rotation = e.rotation;
                    if (rotation > 180) {
                        rotation -= 360;
                    } else if (rotation < -180) {
                        rotation += 360;
                    }
                    rotateDirection = rotation > 0 ? 1 : rotation < 0 ? -1 : 0;
                });
                hammerManager.on("rotateend",
                function(e) {
                    if (atRotation) return;
                    if (Math.abs(rotation) > 30) {
                        if (rotateDirection == 1) {
                            rotateCW(e.center);
                        } else if (rotateDirection == -1) {
                            rotateCCW(e.center);
                        }
                    }
                });
            } else {
                $moveLayer.on("dblclick",
                function(e) {
                    rotateCW({
                        x: e.clientX,
                        y: e.clientY
                    });
                });
            }
        }
        function rotateCW(point) {
            rotateBy(90, point);
        }
        function rotateCCW(point) {
            rotateBy( - 90, point);
        }
        function rotateBy(angle, point) {
            if (atRotation) return;
            atRotation = true;
            var loacl;
            if (!point) {
                loacl = loaclToLoacl($moveLayer, $clipView, clipWidth * .5, clipHeight * .5);
            } else {
                loacl = globalToLoacl($moveLayer, point.x, point.y);
            }
            var origin = calculateOrigin(curAngle, loacl),
            originX = origin.x,
            originY = origin.y,
            offsetX = 0,
            offsetY = 0,
            parentOffsetX = 0,
            parentOffsetY = 0,
            newAngle = curAngle + angle,
            curImgWidth,
            curImgHeight;
            if (newAngle == 90 || newAngle == -270) {
                offsetX = originX + originY;
                offsetY = originY - originX;
                if (newAngle > curAngle) {
                    parentOffsetX = imgHeight - originX - originY;
                    parentOffsetY = originX - originY;
                } else if (newAngle < curAngle) {
                    parentOffsetX = (imgHeight - originY) - (imgWidth - originX);
                    parentOffsetY = originX + originY - imgHeight;
                }
                curImgWidth = imgHeight;
                curImgHeight = imgWidth;
            } else if (newAngle == 180 || newAngle == -180) {
                offsetX = originX * 2;
                offsetY = originY * 2;
                if (newAngle > curAngle) {
                    parentOffsetX = (imgWidth - originX) - (imgHeight - originY);
                    parentOffsetY = imgHeight - (originX + originY);
                } else if (newAngle < curAngle) {
                    parentOffsetX = imgWidth - (originX + originY);
                    parentOffsetY = (imgHeight - originY) - (imgWidth - originX);
                }
                curImgWidth = imgWidth;
                curImgHeight = imgHeight;
            } else if (newAngle == 270 || newAngle == -90) {
                offsetX = originX - originY;
                offsetY = originX + originY;
                if (newAngle > curAngle) {
                    parentOffsetX = originX + originY - imgWidth;
                    parentOffsetY = (imgWidth - originX) - (imgHeight - originY);
                } else if (newAngle < curAngle) {
                    parentOffsetX = originY - originX;
                    parentOffsetY = imgWidth - originX - originY;
                }
                curImgWidth = imgHeight;
                curImgHeight = imgWidth;
            } else if (newAngle == 0 || newAngle == 360 || newAngle == -360) {
                offsetX = 0;
                offsetY = 0;
                if (newAngle > curAngle) {
                    parentOffsetX = originX - originY;
                    parentOffsetY = originX + originY - imgWidth;
                } else if (newAngle < curAngle) {
                    parentOffsetX = originX + originY - imgHeight;
                    parentOffsetY = originY - originX;
                }
                curImgWidth = imgWidth;
                curImgHeight = imgHeight;
            }
            if (curAngle == 0) {
                curX = 0;
                curY = 0;
            } else if (curAngle == 90 || curAngle == -270) {
                curX -= originX + originY;
                curY -= originY - originX;
            } else if (curAngle == 180 || curAngle == -180) {
                curX -= originX * 2;
                curY -= originY * 2;
            } else if (curAngle == 270 || curAngle == -90) {
                curX -= originX - originY;
                curY -= originX + originY;
            }
            curX = curX.toFixed(2) - 0;
            curY = curY.toFixed(2) - 0;
            setTransform($rotateLayer, curX, curY, curAngle, originX, originY);
            setTransition($rotateLayer, curX, curY, newAngle, 200,
            function() {
                atRotation = false;
                curAngle = newAngle % 360;
                curX += offsetX + parentOffsetX;
                curY += offsetY + parentOffsetY;
                curX = curX.toFixed(2) - 0;
                curY = curY.toFixed(2) - 0;
                setTransform($rotateLayer, curX, curY, curAngle);
                myScroll.scrollTo(myScroll.x - parentOffsetX * myScroll.scale, myScroll.y - parentOffsetY * myScroll.scale);
                calculateScale(curImgWidth, curImgHeight);
                if (myScroll.scale < myScroll.options.zoomMin) {
                    myScroll.zoom(myScroll.options.zoomMin);
                }
                refreshScroll(curImgWidth, curImgHeight);
            });
        }
        function initClip() {
            canvas = document.createElement("canvas");
        }
        function clipImg() {
            if (!imgLoaded) {
                alert("亲，当前没有图片可以裁剪!");
                return;
            }
            var local = loaclToLoacl($moveLayer, $clipView);
            var scale = myScroll.scale;
            var ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.save();
            if (!outputWidth || !outputHeight) {
                canvas.width = clipWidth / scale;
                canvas.height = clipHeight / scale;
            } else {
                canvas.width = outputWidth;
                canvas.height = outputHeight;
                ctx.scale(outputWidth / clipWidth * scale, outputHeight / clipHeight * scale);
            }
            ctx.translate(curX - local.x / scale, curY - local.y / scale);
            ctx.rotate(curAngle * Math.PI / 180);
            ctx.drawImage($img[0], 0, 0);
            ctx.restore();
            var dataURL = canvas.toDataURL(outputType, 1);
            $view.css("background-image", "url(" + dataURL + ")");
            clipFinish.call($img[0], dataURL);
        }
        function resize() {
            hideAction($container,
            function() {
                containerWidth = $container.width();
                containerHeight = $container.height();
            });
        }
        function loaclToLoacl($layerOne, $layerTwo, x, y) {
            x = x || 0;
            y = y || 0;
            var layerOneOffset, layerTwoOffset;
            hideAction($layerOne,
            function() {
                layerOneOffset = $layerOne.offset();
            });
            hideAction($layerTwo,
            function() {
                layerTwoOffset = $layerTwo.offset();
            });
            return {
                x: layerTwoOffset.left - layerOneOffset.left + x,
                y: layerTwoOffset.top - layerOneOffset.top + y
            };
        }
        function globalToLoacl($layer, x, y) {
            x = x || 0;
            y = y || 0;
            var layerOffset;
            hideAction($layer,
            function() {
                layerOffset = $layer.offset();
            });
            return {
                x: x + $win.scrollLeft() - layerOffset.left,
                y: y + $win.scrollTop() - layerOffset.top
            };
        }
        function hideAction(jq, func) {
            var $hide = $();
            $.each(jq,
            function(i, n) {
                var $n = $(n);
                var $hidden = $n.parents().andSelf().filter(":hidden");
                var $none;
                for (var i = 0; i < $hidden.length; i++) {
                    if (!$n.is(":hidden")) break;
                    $none = $hidden.eq(i);
                    if ($none.css("display") == "none") $hide = $hide.add($none.show());
                }
            });
            if (typeof(func) == "function") func.call(this);
            $hide.hide();
        }
        function calculateOrigin(curAngle, point) {
            var scale = myScroll.scale;
            var origin = {};
            if (curAngle == 0) {
                origin.x = point.x / scale;
                origin.y = point.y / scale;
            } else if (curAngle == 90 || curAngle == -270) {
                origin.x = point.y / scale;
                origin.y = imgHeight - point.x / scale;
            } else if (curAngle == 180 || curAngle == -180) {
                origin.x = imgWidth - point.x / scale;
                origin.y = imgHeight - point.y / scale;
            } else if (curAngle == 270 || curAngle == -90) {
                origin.x = imgWidth - point.y / scale;
                origin.y = point.x / scale;
            }
            return origin;
        }
        function getScale(w1, h1, w2, h2) {
            var sx = w1 / w2;
            var sy = h1 / h2;
            return sx > sy ? sx: sy;
        }
        function calculateScale(width, height) {
            myScroll.options.zoomMin = getScale(clipWidth, clipHeight, width, height);
            myScroll.options.zoomMax = Math.max(1, myScroll.options.zoomMin);
            myScroll.options.zoomStart = Math.min(myScroll.options.zoomMax, getScale(containerWidth, containerHeight, width, height));
        }
        function clearImg() {
            if ($img && $img.length) {
                $img.remove();
                delete $img[0];
            }
        }
        function createImg(src) {
            clearImg();
            $img = $("<img>").css({
                "user-select": "none",
                "pointer-events": "none"
            });
            $img.load(imgLoad);
            $img.attr("src", src);
        }
        function setTransform($obj, x, y, angle, originX, originY) {
            originX = originX || 0;
            originY = originY || 0;
            var style = {};
            style[prefix + "transform"] = "translateZ(0) translate(" + x + "px," + y + "px) rotate(" + angle + "deg)";
            style[prefix + "transform-origin"] = originX + "px " + originY + "px";
            $obj.css(style);
        }
        function setTransition($obj, x, y, angle, dur, fn) {
            $obj.css(prefix + "transform");
            $obj.css(prefix + "transition", prefix + "transform " + dur + "ms");
            $obj.one(transitionEnd,
            function() {
                $obj.css(prefix + "transition", "");
                fn.call(this);
            });
            $obj.css(prefix + "transform", "translateZ(0) translate(" + x + "px," + y + "px) rotate(" + angle + "deg)");
        }
        function isArray(obj) {
            return Object.prototype.toString.call(obj) === "[object Array]";
        }
        function init() {
            $container = $(container).css({
                "user-select": "none",
                "overflow": "hidden"
            });
            if ($container.css("position") == "static") $container.css("position", "relative");
            $clipView = $("<div class='photo-clip-view'>").css({
                "position": "relative",
                "left": "0",
                "top": "0",
                "width": clipWidth,
                "height": clipHeight,
                "margin":"0 auto"
            }).appendTo($container);
            $moveLayer = $("<div class='photo-clip-moveLayer'>").appendTo($clipView);
            $rotateLayer = $("<div class='photo-clip-rotateLayer'>").appendTo($moveLayer);
            var $mask = $("<div class='photo-clip-mask'>").css({
                "position": "absolute",
                "left": 0,
                "top": 0,
                "width": "100%",
                "height": "100%",
                "pointer-events": "none"
            }).appendTo($container);
            var $mask_left = $("<div class='photo-clip-mask-left'>").css({
                "position": "absolute",
                "left": 0,
                "right": "50%",
                "top": "50%",
                "bottom": "50%",
                "width": "auto",
                "height": clipHeight,
                "margin-right": clipWidth / 2,
                "margin-top": -clipHeight / 2,
                "margin-bottom": -clipHeight / 2,
                "background-color": "rgba(0,0,0,.5)"
            }).appendTo($mask);
            var $mask_right = $("<div class='photo-clip-mask-right'>").css({
                "position": "absolute",
                "left": "50%",
                "right": 0,
                "top": "50%",
                "bottom": "50%",
                "margin-left": clipWidth / 2,
                "margin-top": -clipHeight / 2,
                "margin-bottom": -clipHeight / 2,
                "background-color": "rgba(0,0,0,.5)"
            }).appendTo($mask);
            var $mask_top = $("<div class='photo-clip-mask-top'>").css({
                "position": "absolute",
                "left": 0,
                "right": 0,
                "top": 0,
                "bottom": "50%",
                "margin-bottom": clipHeight / 2,
                "background-color": "rgba(0,0,0,.5)"
            }).appendTo($mask);
            var $mask_bottom = $("<div class='photo-clip-mask-bottom'>").css({
                "position": "absolute",
                "left": 0,
                "right": 0,
                "top": "50%",
                "bottom": 0,
                "margin-top": clipHeight / 2,
                "background-color": "rgba(0,0,0,.5)"
            }).appendTo($mask);
            var $clip_area = $("<div class='photo-clip-area'>").css({
                "border": "1px dashed #ddd",
                "position": "absolute",
                "left": "50%",
                "top": "50%",
                "width": clipWidth,
                "height": clipHeight,
                "margin-left": -clipWidth / 2 - 1,
                "margin-top": -clipHeight / 2 - 1
            }).appendTo($mask);
            $view = $(view);
            if ($view.length) {
                $view.css({
                    "background-color": "#666",
                    "background-repeat": "no-repeat",
                    "background-position": "center",
                    "background-size": "contain"
                });
            }
        }
        function destroy() {
            $file.off("change");
            $file = null;
            if (hammerManager) {
                hammerManager.off("rotatemove");
                hammerManager.off("rotateend");
                hammerManager = null;
            } else {
                $moveLayer.off("dblclick");
            }
            myScroll.destroy();
            myScroll = null;
            $container.empty();
            $container = null;
            $clipView = null;
            $moveLayer = null;
            $rotateLayer = null;
            $view.css({
                "background-color": "",
                "background-repeat": "",
                "background-position": "",
                "background-size": ""
            });
            $view = null;
        }
        return {
            destroy: destroy
        };
    }
    var prefix = '',
    transitionEnd; (function() {
        var eventPrefix, vendors = {
            Webkit: 'webkit',
            Moz: '',
            O: 'o'
        },
        testEl = document.documentElement,
        normalizeEvent = function(name) {
            return eventPrefix ? eventPrefix + name: name.toLowerCase()
        };
        for (var i in vendors) {
            if (testEl.style[i + 'TransitionProperty'] !== undefined) {
                prefix = '-' + i.toLowerCase() + '-';
                eventPrefix = vendors[i];
                break;
            }
        }
        transitionEnd = normalizeEvent('TransitionEnd');
    })();
    return PhotoClip;
}));