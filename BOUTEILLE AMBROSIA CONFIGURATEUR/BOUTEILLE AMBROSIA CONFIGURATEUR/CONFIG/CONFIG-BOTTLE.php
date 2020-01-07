<!DOCTYPE html>
<html lang="en">
<head>
    <title>Configurateur</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <link type="text/css" rel="stylesheet" href="main.css">

</head>

<body>


<div class="parent">

    <div id="info">

        <div class="inter" style="width : 450px;">

            <span>Choisir un Produit:
                <select class="select-css" id="body-models">
                    <option id="e-liquide">E-Liquide</option>
                    <option id="carte">Carte de visite</option>
                </select>
            </span>
            <br>

            <span>Choisissez parmis les nombreux choix de design: <select class="select-css" id="body-mat"></select></span>

            <br>
            <div class="grille" id="grille">



            </div>
        </div>
    </div>
</div>

<div id="container">

</div>


<script type="module">

    import * as THREE from '../build/three.module.js';
    import Stats from './jsm/libs/stats.module.js';
    import {GLTFLoader} from './jsm/loaders/GLTFLoader.js';
    import {DRACOLoader} from './jsm/loaders/DRACOLoader.js';
    import {OrbitControls} from './jsm/controls/OrbitControls.js';
    import {PMREMGenerator} from './jsm/pmrem/PMREMGenerator.js';
    import {PMREMCubeUVPacker} from './jsm/pmrem/PMREMCubeUVPacker.js';
    import {EquirectangularToCubeGenerator} from './jsm/loaders/EquirectangularToCubeGenerator.js';
    import {RGBELoader} from './jsm/loaders/RGBELoader.js';


    var scnCamera, scene, meshFloor, renderer, objModel, materialsLib, envMap, controls;

    var bodyMatSelect = document.getElementById('body-mat');
    var bodyModelSelect = document.getElementById('body-models');
    var grilleSelect = document.getElementById('grille')
    var liens;
    var modelParts = {
        body: [],
    };

    var scnCameraTarget = new THREE.Vector3();


    function init() {


        var container = document.getElementById('container');

        scene = new THREE.Scene();

        scnCamera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.001, 100);
        scnCamera.position.set(30, 5.8, 0);


        //var urls = [ 'posx.jpg', 'negx.jpg', 'posy.jpg', 'negy.jpg', 'posz.jpg', 'negz.jpg' ];
        //var loader = new THREE.CubeTextureLoader().setPath( 'textures/cube/white/' );

        var urls = ['px.png', 'nx.png', 'py.png', 'ny.png', 'pz.png', 'nz.png'];
        var loader = new THREE.CubeTextureLoader().setPath('textures/cube/show1/');


        loader.load(urls, function (texture) {


            //camera.lookAt( 0, 1, 0 );

            var helper = new THREE.CameraHelper(scnCamera);
            scene.add(helper);


            ////ORBIT CONTROLS////
            var controls = new OrbitControls(scnCamera, renderer.domElement);

            controls.enablePan = false;
            controls.enableZoom = true;
            controls.minDistance = 1;
            controls.maxDistance = 5;

            controls.enableDamping = true;
            controls.dampingFactor = 0.2;
            controls.minPolarAngle = 0.1; //Uper
            controls.maxPolarAngle = 1.8; //Lowers
            controls.target.set(0, 0.6, 0);
            controls.autoRotate = true;
            controls.autoRotateSpeed = 0.05;
            controls.update();

            ////ORBIT CONTROLS FIN////


            ////FOND IMAGE DE LA SCENE////
            scene = new THREE.Scene();
            scene.background = new THREE.TextureLoader().load("textures/fond-noir.jpg"); // (Ciel)
            scene.backgroundSphere = true;


            ////////

            ////SOL////
            meshFloor = new THREE.Mesh(
                new THREE.PlaneGeometry(10, 10, 10, 10),
                new THREE.MeshStandardMaterial({color: 0x0C0C0C, wireframe: false, roughness: 1.0,})
            );
            meshFloor.rotation.x -= Math.PI / 2;
            meshFloor.receiveShadow = true;
            scene.add(meshFloor);
            ////FIN SOL////

            ////LIGHT////

            var light = new THREE.HemisphereLight(0xffffff, 0x444444);
            light.position.set(0, 10, 0);
            scene.add(light);

            ////AMBIENT LIGHT////
            var ambient = new THREE.AmbientLight(0xffffff, 1.5);
            scene.add(ambient);
            ////AMBIENT LIGHT FIN////

            //LIGHT 1 LUMIERE ROUGE//


            var SpotLight = new THREE.SpotLight(0xffffff, 3);
            SpotLight.position.set(5, 2, 2);
            SpotLight.target.position.set(0, 0, 0);
            SpotLight.castShadow = true;
            SpotLight.shadowCameraVisible = false;
            SpotLight.decay = 1;
            SpotLight.penumbra = 1;

            scene.add(SpotLight);

            //var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0xFF0000 ) ;
            //scene.add( spotLightHelper );

            //LIGHT 2 LUMIERE VERTE//


            var SpotLight = new THREE.SpotLight(0xffffff, 5);
            SpotLight.position.set(5, 2, 10);
            SpotLight.target.position.set(0, 0, 0);
            SpotLight.castShadow = true;
            SpotLight.decay = 1;
            SpotLight.penumbra = 1;

            scene.add(SpotLight);

            //var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0x64FF00 ) ;
            //scene.add( spotLightHelper );


            //LIGHT 3 LUMIERE BLEU//


            var SpotLight = new THREE.SpotLight(0xffffff, 2);
            SpotLight.position.set(-5, 1, 1);
            SpotLight.target.position.set(0, 0, 0);
            SpotLight.castShadow = true;
            SpotLight.decay = 1;
            SpotLight.penumbra = 1;

            scene.add(SpotLight);

            //var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0x00C9FF ) ;
            //scene.add( spotLightHelper );


            //LIGHT 4 LUMIERE JAUNE//


            var SpotLight = new THREE.SpotLight(0xffffff, 5);
            SpotLight.position.set(-5, 5, -15);
            SpotLight.target.position.set(0, 0, 0);
            SpotLight.castShadow = true;
            SpotLight.decay = 1;
            SpotLight.penumbra = 1;

            scene.add(SpotLight);

            //var spotLightHelper = new THREE.SpotLightHelper( SpotLight, 0xFFFF00 ) ;
            //scene.add( spotLightHelper );


            ////////LIGHT FIN////


            //////LIGHT AUTRE REGLAGE////


            //var ambientLight = new THREE.AmbientLight (0xffffff, 0.2);
            //scene.add (ambientLight);

            //var light = new THREE.PointLight (0xffffff, 0.8, 18);
            //light.position.set (-3,6,-3);
            //light.castShadow = true;
            //light.shadow.camera.near = 0.1;
            //light.shadow.camera.far = 25;
            //scene.add(light);


            /////////////////////////////

            var pmremGenerator = new PMREMGenerator(texture);
            pmremGenerator.update(renderer);


            var pmremCubeUVPacker = new PMREMCubeUVPacker(pmremGenerator.cubeLods);
            pmremCubeUVPacker.update(renderer);

            envMap = pmremCubeUVPacker.CubeUVRenderTarget.texture;

            pmremGenerator.dispose();
            pmremCubeUVPacker.dispose();


            //

            initModel();
            initMaterials();
            initMaterialSelectionMenus();

        });


        ////RENDER ENGINE ////

        renderer = new THREE.WebGLRenderer({antialias: true});
        renderer = new THREE.WebGLRenderer({alpha: true});
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.gammaOutput = true;
        renderer.physicallyCorrectLights = true;
        renderer.gammaInput = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.setClearColor(0x000000, 1);
        renderer.shadowMap.enabled = true;
        container.appendChild(renderer.domElement);

        ////RENDER ENGINE FIN////


        window.addEventListener('resize', onWindowResize, false);

        renderer.setAnimationLoop(function () {

            update();

            renderer.render(scene, scnCamera);

        });


    }


    function initModel() {
        var dracoLoader = new DRACOLoader();
        dracoLoader.setDecoderPath('js/libs/draco/gltf/');

        var loader = new GLTFLoader();
        loader.setDRACOLoader(dracoLoader);

        loader.load('models/gltf/ambrosia/bouteille-ambrosia.gltf', function (gltf) {

            objModel = gltf.scene.children[0];

            objModel.traverse(function (child) {
                if (child.isMesh) {
                    child.material.envMap = envMap;
                }

            });

            scene.add(objModel);

            // car parts for material selection
            modelParts.body.push(objModel.getObjectByName('body'));

            updateMaterials();

        });

    }

    function updateModel(){
        scene.remove(objModel)
        var dracoLoader = new DRACOLoader();
        dracoLoader.setDecoderPath('js/libs/draco/gltf/');

        var loader = new GLTFLoader();
        loader.setDRACOLoader(dracoLoader);
        //liens des objets 3D
        if(bodyModelSelect.value === "Carte de visite"){
            liens = "models/gltf/carte/carte.gltf"
        }else if(bodyModelSelect.value === "E-Liquide"){
            liens = "models/gltf/ambrosia/bouteille-ambrosia.gltf"
        }

        loader.load(liens, function (gltf) {

            objModel = gltf.scene.children[0];


            objModel.traverse(function (child) {

                if (child.isMesh) {
                    child.material.envMap = envMap;
                }
            });

            scene.add(objModel);

            // car parts for material selection
            modelParts.body.push(objModel.getObjectByName('body'));

            updateMaterials();
        });
    }
    bodyModelSelect.addEventListener('change', updateModel);

    function initMaterials() {

        materialsLib = {

            main: [
                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-belle-prune.jpg"),
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-belle-prune-rgh.jpg"),
                        metalness: 0.0,

                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Belle Prune'
                    }),

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-boree.jpg"),
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-boree-rgh.jpg"),
                        metalness: 0.0,

                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Borée'
                    }),

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-crepe.jpg"),
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-crepe-rgh.jpg"),
                        metalness: 0.0,

                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Crêpe'
                    }),

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-cupcake.jpg"),
                        metalness: 0.0,
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-cupcake-rgh.jpg"),

                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Cupcake'
                    }),

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-euros.jpg"),
                        metalness: 0.0,
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-euros-rgh.jpg"),
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Euros'
                    }),

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-notos.jpg"),
                        metalness: 0.0,
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-notos-rgh.jpg"),
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Notos'
                    }),

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-super-beignet.jpg"),
                        metalness: 0.0,
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-super-beignet-rgh.jpg"),
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Super Beignet'
                    }),


                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load("etiquettes/etiquette-zephyr.jpg"),
                        metalness: 0.0,
                        roughnessMap: new THREE.TextureLoader().load("etiquettes/etiquette-zephyr-rgh.jpg"),
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Étiquette Zephyr'
                    }),

            ],
            Carte : [

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/dorure-or-noir/carte-color.jpg" ),
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-noir/carte-rgh.jpg" ),
                        metalnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-noir/carte-metal.jpg" ),
                        normalMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-noir/carte-normal.jpg" ) ,
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Finition dorée sur fond noir' } ),

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/dorure-or-blanc/carte-color-blanc.jpg" ),
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-blanc/carte-rgh-blanc.jpg" ),
                        metalnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-blanc/carte-metal-blanc.jpg" ),
                        normalMap: new THREE.TextureLoader().load( "cartetextures/dorure-or-blanc/carte-normal-blanc.jpg" ) ,
                        envMap: envMap,
                        envMapIntensity: 0.0,
                        name: 'Finition dorée sur fond blanc' } ),

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/dorure-argent-noir/carte-color-argent.jpg" ),
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-argent-noir/carte-rgh-argent.jpg" ),
                        metalnessMap: new THREE.TextureLoader().load( "cartetextures/dorure-argent-noir/carte-metal-argent.jpg" ),
                        normalMap: new THREE.TextureLoader().load( "cartetextures/dorure-argent-noir/carte-normal-argent.jpg" ),
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Finition dorée argent sur fond noir'  } ),

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/vernis-selectif/vernis-color.jpg" ),
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/vernis-selectif/vernis-rgh.jpg" ),
                        metalnessMap: 0.0,
                        envMap: envMap,
                        envMapIntensity: 0.4,
                        name: 'Vernis sélectif' } ),

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/vernis-3d/vernis-3d-color.jpg" ),
                        metalnessMap: 0.0,
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/vernis-3d/vernis-3d-rgh.jpg" ),
                        normalMap: new THREE.TextureLoader().load( "cartetextures/vernis-3d/vernis-3d-normal.jpg" ) ,
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Vernis sélectif 3D' } ),

                new THREE.MeshStandardMaterial(
                    {
                        map: new THREE.TextureLoader().load( "cartetextures/gauffrage/gauffrage-color.jpg" ),
                        metalnessMap:0.0,
                        roughnessMap: new THREE.TextureLoader().load( "cartetextures/gauffrage/gauffrage-rgh.jpg" ),
                        normalMap: new THREE.TextureLoader().load( "cartetextures/gauffrage/gauffrage-normal.jpg" ) ,
                        envMap: envMap,
                        envMapIntensity: 0.1,
                        name: 'Gauffrage' } ),
            ],
        };
    }

    function initMaterialSelectionMenus() {

        function addOption(name, menu) {

            var option = document.createElement('option');
            option.text = name;
            option.value = name;
            menu.add(option);

        }
            if(bodyModelSelect.value === "Carte de visite"){
                bodyMatSelect.innerHTML=""
                grilleSelect.style.display = "none"
                materialsLib.Carte.forEach(function (material) {

                    addOption(material.name, bodyMatSelect);

                });

                bodyMatSelect.selectedIndex = 0;
            }else if(bodyModelSelect.value === "E-Liquide"){
                bodyMatSelect.innerHTML=""
                grilleSelect.style.display = ""
                affichageGrille()
                materialsLib.main.forEach(function (material) {

                    addOption(material.name, bodyMatSelect);

                });
                bodyMatSelect.selectedIndex = 0;
            }

    }
    bodyMatSelect.addEventListener('change', updateMaterials);
    bodyModelSelect.addEventListener('change', initMaterialSelectionMenus);

    // set materials to the current values of the selection menus
    function updateMaterials() {
        if(bodyModelSelect.value === "E-Liquide") {
            var bodyMat = materialsLib.main[bodyMatSelect.selectedIndex];

            modelParts.body.forEach(part => part.material = bodyMat);
        }else if (bodyModelSelect.value === "Carte de visite") {
            var bodyMatt = materialsLib.Carte[bodyMatSelect.selectedIndex];

            modelParts.body.forEach(part => part.material = bodyMatt);

        }
    }
//Affichage de la grille avec liens .usdz
    function affichageGrille(){
        let src={
            0:'miniatures/belle-prune.jpg',
            1:'miniatures/boree.jpg',
            2:'miniatures/crepe.jpg',
            3:'miniatures/cupcake.jpg',
            4:'miniatures/euros.jpg',
            5:'miniatures/notos.jpg',
            6:'miniatures/super-beignet.jpg',
            7:'miniatures/zephyr.jpg'
        }
        grilleSelect.style.display="";
        grilleSelect.innerHTML=null;
        for (let i = 0; i < 8; i++) {
            grilleSelect.innerHTML+= '<a href="usdz/bottle-'+i+1+'.usdz">'+'<img class="miniature" src="'+src[i]+'"></a>'
        }
    }


    function onWindowResize() {

        scnCamera.aspect = window.innerWidth / window.innerHeight;
        scnCamera.updateProjectionMatrix();

        renderer.setSize(window.innerWidth, window.innerHeight);

    }

    function update() {


    }


    function animate() {
        requestAnimationFrame(animate);
        controls.update(); // only required if controls.enableDamping = true, or if controls.autoRotate = true
        render();
    }


    function render() {

        renderer.render(scene, scnCamera);


    }

    init();


</script>

</body>
</html>
