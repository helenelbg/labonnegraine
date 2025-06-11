/*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*/
var unicode_hack = (function() {
    /* Regexps to match characters in the BMP according to their Unicode category.
       Extracted from Unicode specification, version 5.0.0, source:
       http://unicode.org/versions/Unicode5.0.0/
    */
	var unicodeCategories = {
		Pi:'[\u00ab\u2018\u201b\u201c\u201f\u2039\u2e02\u2e04\u2e09\u2e0c\u2e1c]',
		Sk:'[\u005e\u0060\u00a8\u00af\u00b4\u00b8\u02c2-\u02c5\u02d2-\u02df\u02e5-\u02ed\u02ef-\u02ff\u0374\u0375\u0384\u0385\u1fbd\u1fbf-\u1fc1\u1fcd-\u1fcf\u1fdd-\u1fdf\u1fed-\u1fef\u1ffd\u1ffe\u309b\u309c\ua700-\ua716\ua720\ua721\uff3e\uff40\uffe3]',
		Sm:'[\u002b\u003c-\u003e\u007c\u007e\u00ac\u00b1\u00d7\u00f7\u03f6\u2044\u2052\u207a-\u207c\u208a-\u208c\u2140-\u2144\u214b\u2190-\u2194\u219a\u219b\u21a0\u21a3\u21a6\u21ae\u21ce\u21cf\u21d2\u21d4\u21f4-\u22ff\u2308-\u230b\u2320\u2321\u237c\u239b-\u23b3\u23dc-\u23e1\u25b7\u25c1\u25f8-\u25ff\u266f\u27c0-\u27c4\u27c7-\u27ca\u27d0-\u27e5\u27f0-\u27ff\u2900-\u2982\u2999-\u29d7\u29dc-\u29fb\u29fe-\u2aff\ufb29\ufe62\ufe64-\ufe66\uff0b\uff1c-\uff1e\uff5c\uff5e\uffe2\uffe9-\uffec]',
		So:'[\u00a6\u00a7\u00a9\u00ae\u00b0\u00b6\u0482\u060e\u060f\u06e9\u06fd\u06fe\u07f6\u09fa\u0b70\u0bf3-\u0bf8\u0bfa\u0cf1\u0cf2\u0f01-\u0f03\u0f13-\u0f17\u0f1a-\u0f1f\u0f34\u0f36\u0f38\u0fbe-\u0fc5\u0fc7-\u0fcc\u0fcf\u1360\u1390-\u1399\u1940\u19e0-\u19ff\u1b61-\u1b6a\u1b74-\u1b7c\u2100\u2101\u2103-\u2106\u2108\u2109\u2114\u2116-\u2118\u211e-\u2123\u2125\u2127\u2129\u212e\u213a\u213b\u214a\u214c\u214d\u2195-\u2199\u219c-\u219f\u21a1\u21a2\u21a4\u21a5\u21a7-\u21ad\u21af-\u21cd\u21d0\u21d1\u21d3\u21d5-\u21f3\u2300-\u2307\u230c-\u231f\u2322-\u2328\u232b-\u237b\u237d-\u239a\u23b4-\u23db\u23e2-\u23e7\u2400-\u2426\u2440-\u244a\u249c-\u24e9\u2500-\u25b6\u25b8-\u25c0\u25c2-\u25f7\u2600-\u266e\u2670-\u269c\u26a0-\u26b2\u2701-\u2704\u2706-\u2709\u270c-\u2727\u2729-\u274b\u274d\u274f-\u2752\u2756\u2758-\u275e\u2761-\u2767\u2794\u2798-\u27af\u27b1-\u27be\u2800-\u28ff\u2b00-\u2b1a\u2b20-\u2b23\u2ce5-\u2cea\u2e80-\u2e99\u2e9b-\u2ef3\u2f00-\u2fd5\u2ff0-\u2ffb\u3004\u3012\u3013\u3020\u3036\u3037\u303e\u303f\u3190\u3191\u3196-\u319f\u31c0-\u31cf\u3200-\u321e\u322a-\u3243\u3250\u3260-\u327f\u328a-\u32b0\u32c0-\u32fe\u3300-\u33ff\u4dc0-\u4dff\ua490-\ua4c6\ua828-\ua82b\ufdfd\uffe4\uffe8\uffed\uffee\ufffc\ufffd]',
		Po:'[\u0021-\u0023\u0025-\u0027\u002a\u002c\u002e\u002f\u003a\u003b\u003f\u0040\u005c\u00a1\u00b7\u00bf\u037e\u0387\u055a-\u055f\u0589\u05be\u05c0\u05c3\u05c6\u05f3\u05f4\u060c\u060d\u061b\u061e\u061f\u066a-\u066d\u06d4\u0700-\u070d\u07f7-\u07f9\u0964\u0965\u0970\u0df4\u0e4f\u0e5a\u0e5b\u0f04-\u0f12\u0f85\u0fd0\u0fd1\u104a-\u104f\u10fb\u1361-\u1368\u166d\u166e\u16eb-\u16ed\u1735\u1736\u17d4-\u17d6\u17d8-\u17da\u1800-\u1805\u1807-\u180a\u1944\u1945\u19de\u19df\u1a1e\u1a1f\u1b5a-\u1b60\u2016\u2017\u2020-\u2027\u2030-\u2038\u203b-\u203e\u2041-\u2043\u2047-\u2051\u2053\u2055-\u205e\u2cf9-\u2cfc\u2cfe\u2cff\u2e00\u2e01\u2e06-\u2e08\u2e0b\u2e0e-\u2e16\u3001-\u3003\u303d\u30fb\ua874-\ua877\ufe10-\ufe16\ufe19\ufe30\ufe45\ufe46\ufe49-\ufe4c\ufe50-\ufe52\ufe54-\ufe57\ufe5f-\ufe61\ufe68\ufe6a\ufe6b\uff01-\uff03\uff05-\uff07\uff0a\uff0c\uff0e\uff0f\uff1a\uff1b\uff1f\uff20\uff3c\uff61\uff64\uff65]',
		Mn:'[\u0300-\u036f\u0483-\u0486\u0591-\u05bd\u05bf\u05c1\u05c2\u05c4\u05c5\u05c7\u0610-\u0615\u064b-\u065e\u0670\u06d6-\u06dc\u06df-\u06e4\u06e7\u06e8\u06ea-\u06ed\u0711\u0730-\u074a\u07a6-\u07b0\u07eb-\u07f3\u0901\u0902\u093c\u0941-\u0948\u094d\u0951-\u0954\u0962\u0963\u0981\u09bc\u09c1-\u09c4\u09cd\u09e2\u09e3\u0a01\u0a02\u0a3c\u0a41\u0a42\u0a47\u0a48\u0a4b-\u0a4d\u0a70\u0a71\u0a81\u0a82\u0abc\u0ac1-\u0ac5\u0ac7\u0ac8\u0acd\u0ae2\u0ae3\u0b01\u0b3c\u0b3f\u0b41-\u0b43\u0b4d\u0b56\u0b82\u0bc0\u0bcd\u0c3e-\u0c40\u0c46-\u0c48\u0c4a-\u0c4d\u0c55\u0c56\u0cbc\u0cbf\u0cc6\u0ccc\u0ccd\u0ce2\u0ce3\u0d41-\u0d43\u0d4d\u0dca\u0dd2-\u0dd4\u0dd6\u0e31\u0e34-\u0e3a\u0e47-\u0e4e\u0eb1\u0eb4-\u0eb9\u0ebb\u0ebc\u0ec8-\u0ecd\u0f18\u0f19\u0f35\u0f37\u0f39\u0f71-\u0f7e\u0f80-\u0f84\u0f86\u0f87\u0f90-\u0f97\u0f99-\u0fbc\u0fc6\u102d-\u1030\u1032\u1036\u1037\u1039\u1058\u1059\u135f\u1712-\u1714\u1732-\u1734\u1752\u1753\u1772\u1773\u17b7-\u17bd\u17c6\u17c9-\u17d3\u17dd\u180b-\u180d\u18a9\u1920-\u1922\u1927\u1928\u1932\u1939-\u193b\u1a17\u1a18\u1b00-\u1b03\u1b34\u1b36-\u1b3a\u1b3c\u1b42\u1b6b-\u1b73\u1dc0-\u1dca\u1dfe\u1dff\u20d0-\u20dc\u20e1\u20e5-\u20ef\u302a-\u302f\u3099\u309a\ua806\ua80b\ua825\ua826\ufb1e\ufe00-\ufe0f\ufe20-\ufe23]',
		Ps:'[\u0028\u005b\u007b\u0f3a\u0f3c\u169b\u201a\u201e\u2045\u207d\u208d\u2329\u2768\u276a\u276c\u276e\u2770\u2772\u2774\u27c5\u27e6\u27e8\u27ea\u2983\u2985\u2987\u2989\u298b\u298d\u298f\u2991\u2993\u2995\u2997\u29d8\u29da\u29fc\u3008\u300a\u300c\u300e\u3010\u3014\u3016\u3018\u301a\u301d\ufd3e\ufe17\ufe35\ufe37\ufe39\ufe3b\ufe3d\ufe3f\ufe41\ufe43\ufe47\ufe59\ufe5b\ufe5d\uff08\uff3b\uff5b\uff5f\uff62]',
		Cc:'[\u0000-\u001f\u007f-\u009f]',
		Cf:'[\u00ad\u0600-\u0603\u06dd\u070f\u17b4\u17b5\u200b-\u200f\u202a-\u202e\u2060-\u2063\u206a-\u206f\ufeff\ufff9-\ufffb]',
		Ll:'[\u0061-\u007a\u00aa\u00b5\u00ba\u00df-\u00f6\u00f8-\u00ff\u0101\u0103\u0105\u0107\u0109\u010b\u010d\u010f\u0111\u0113\u0115\u0117\u0119\u011b\u011d\u011f\u0121\u0123\u0125\u0127\u0129\u012b\u012d\u012f\u0131\u0133\u0135\u0137\u0138\u013a\u013c\u013e\u0140\u0142\u0144\u0146\u0148\u0149\u014b\u014d\u014f\u0151\u0153\u0155\u0157\u0159\u015b\u015d\u015f\u0161\u0163\u0165\u0167\u0169\u016b\u016d\u016f\u0171\u0173\u0175\u0177\u017a\u017c\u017e-\u0180\u0183\u0185\u0188\u018c\u018d\u0192\u0195\u0199-\u019b\u019e\u01a1\u01a3\u01a5\u01a8\u01aa\u01ab\u01ad\u01b0\u01b4\u01b6\u01b9\u01ba\u01bd-\u01bf\u01c6\u01c9\u01cc\u01ce\u01d0\u01d2\u01d4\u01d6\u01d8\u01da\u01dc\u01dd\u01df\u01e1\u01e3\u01e5\u01e7\u01e9\u01eb\u01ed\u01ef\u01f0\u01f3\u01f5\u01f9\u01fb\u01fd\u01ff\u0201\u0203\u0205\u0207\u0209\u020b\u020d\u020f\u0211\u0213\u0215\u0217\u0219\u021b\u021d\u021f\u0221\u0223\u0225\u0227\u0229\u022b\u022d\u022f\u0231\u0233-\u0239\u023c\u023f\u0240\u0242\u0247\u0249\u024b\u024d\u024f-\u0293\u0295-\u02af\u037b-\u037d\u0390\u03ac-\u03ce\u03d0\u03d1\u03d5-\u03d7\u03d9\u03db\u03dd\u03df\u03e1\u03e3\u03e5\u03e7\u03e9\u03eb\u03ed\u03ef-\u03f3\u03f5\u03f8\u03fb\u03fc\u0430-\u045f\u0461\u0463\u0465\u0467\u0469\u046b\u046d\u046f\u0471\u0473\u0475\u0477\u0479\u047b\u047d\u047f\u0481\u048b\u048d\u048f\u0491\u0493\u0495\u0497\u0499\u049b\u049d\u049f\u04a1\u04a3\u04a5\u04a7\u04a9\u04ab\u04ad\u04af\u04b1\u04b3\u04b5\u04b7\u04b9\u04bb\u04bd\u04bf\u04c2\u04c4\u04c6\u04c8\u04ca\u04cc\u04ce\u04cf\u04d1\u04d3\u04d5\u04d7\u04d9\u04db\u04dd\u04df\u04e1\u04e3\u04e5\u04e7\u04e9\u04eb\u04ed\u04ef\u04f1\u04f3\u04f5\u04f7\u04f9\u04fb\u04fd\u04ff\u0501\u0503\u0505\u0507\u0509\u050b\u050d\u050f\u0511\u0513\u0561-\u0587\u1d00-\u1d2b\u1d62-\u1d77\u1d79-\u1d9a\u1e01\u1e03\u1e05\u1e07\u1e09\u1e0b\u1e0d\u1e0f\u1e11\u1e13\u1e15\u1e17\u1e19\u1e1b\u1e1d\u1e1f\u1e21\u1e23\u1e25\u1e27\u1e29\u1e2b\u1e2d\u1e2f\u1e31\u1e33\u1e35\u1e37\u1e39\u1e3b\u1e3d\u1e3f\u1e41\u1e43\u1e45\u1e47\u1e49\u1e4b\u1e4d\u1e4f\u1e51\u1e53\u1e55\u1e57\u1e59\u1e5b\u1e5d\u1e5f\u1e61\u1e63\u1e65\u1e67\u1e69\u1e6b\u1e6d\u1e6f\u1e71\u1e73\u1e75\u1e77\u1e79\u1e7b\u1e7d\u1e7f\u1e81\u1e83\u1e85\u1e87\u1e89\u1e8b\u1e8d\u1e8f\u1e91\u1e93\u1e95-\u1e9b\u1ea1\u1ea3\u1ea5\u1ea7\u1ea9\u1eab\u1ead\u1eaf\u1eb1\u1eb3\u1eb5\u1eb7\u1eb9\u1ebb\u1ebd\u1ebf\u1ec1\u1ec3\u1ec5\u1ec7\u1ec9\u1ecb\u1ecd\u1ecf\u1ed1\u1ed3\u1ed5\u1ed7\u1ed9\u1edb\u1edd\u1edf\u1ee1\u1ee3\u1ee5\u1ee7\u1ee9\u1eeb\u1eed\u1eef\u1ef1\u1ef3\u1ef5\u1ef7\u1ef9\u1f00-\u1f07\u1f10-\u1f15\u1f20-\u1f27\u1f30-\u1f37\u1f40-\u1f45\u1f50-\u1f57\u1f60-\u1f67\u1f70-\u1f7d\u1f80-\u1f87\u1f90-\u1f97\u1fa0-\u1fa7\u1fb0-\u1fb4\u1fb6\u1fb7\u1fbe\u1fc2-\u1fc4\u1fc6\u1fc7\u1fd0-\u1fd3\u1fd6\u1fd7\u1fe0-\u1fe7\u1ff2-\u1ff4\u1ff6\u1ff7\u2071\u207f\u210a\u210e\u210f\u2113\u212f\u2134\u2139\u213c\u213d\u2146-\u2149\u214e\u2184\u2c30-\u2c5e\u2c61\u2c65\u2c66\u2c68\u2c6a\u2c6c\u2c74\u2c76\u2c77\u2c81\u2c83\u2c85\u2c87\u2c89\u2c8b\u2c8d\u2c8f\u2c91\u2c93\u2c95\u2c97\u2c99\u2c9b\u2c9d\u2c9f\u2ca1\u2ca3\u2ca5\u2ca7\u2ca9\u2cab\u2cad\u2caf\u2cb1\u2cb3\u2cb5\u2cb7\u2cb9\u2cbb\u2cbd\u2cbf\u2cc1\u2cc3\u2cc5\u2cc7\u2cc9\u2ccb\u2ccd\u2ccf\u2cd1\u2cd3\u2cd5\u2cd7\u2cd9\u2cdb\u2cdd\u2cdf\u2ce1\u2ce3\u2ce4\u2d00-\u2d25\ufb00-\ufb06\ufb13-\ufb17\uff41-\uff5a]',
		Lm:'[\u02b0-\u02c1\u02c6-\u02d1\u02e0-\u02e4\u02ee\u037a\u0559\u0640\u06e5\u06e6\u07f4\u07f5\u07fa\u0e46\u0ec6\u10fc\u17d7\u1843\u1d2c-\u1d61\u1d78\u1d9b-\u1dbf\u2090-\u2094\u2d6f\u3005\u3031-\u3035\u303b\u309d\u309e\u30fc-\u30fe\ua015\ua717-\ua71a\uff70\uff9e\uff9f]',
		Lo:'[\u01bb\u01c0-\u01c3\u0294\u05d0-\u05ea\u05f0-\u05f2\u0621-\u063a\u0641-\u064a\u066e\u066f\u0671-\u06d3\u06d5\u06ee\u06ef\u06fa-\u06fc\u06ff\u0710\u0712-\u072f\u074d-\u076d\u0780-\u07a5\u07b1\u07ca-\u07ea\u0904-\u0939\u093d\u0950\u0958-\u0961\u097b-\u097f\u0985-\u098c\u098f\u0990\u0993-\u09a8\u09aa-\u09b0\u09b2\u09b6-\u09b9\u09bd\u09ce\u09dc\u09dd\u09df-\u09e1\u09f0\u09f1\u0a05-\u0a0a\u0a0f\u0a10\u0a13-\u0a28\u0a2a-\u0a30\u0a32\u0a33\u0a35\u0a36\u0a38\u0a39\u0a59-\u0a5c\u0a5e\u0a72-\u0a74\u0a85-\u0a8d\u0a8f-\u0a91\u0a93-\u0aa8\u0aaa-\u0ab0\u0ab2\u0ab3\u0ab5-\u0ab9\u0abd\u0ad0\u0ae0\u0ae1\u0b05-\u0b0c\u0b0f\u0b10\u0b13-\u0b28\u0b2a-\u0b30\u0b32\u0b33\u0b35-\u0b39\u0b3d\u0b5c\u0b5d\u0b5f-\u0b61\u0b71\u0b83\u0b85-\u0b8a\u0b8e-\u0b90\u0b92-\u0b95\u0b99\u0b9a\u0b9c\u0b9e\u0b9f\u0ba3\u0ba4\u0ba8-\u0baa\u0bae-\u0bb9\u0c05-\u0c0c\u0c0e-\u0c10\u0c12-\u0c28\u0c2a-\u0c33\u0c35-\u0c39\u0c60\u0c61\u0c85-\u0c8c\u0c8e-\u0c90\u0c92-\u0ca8\u0caa-\u0cb3\u0cb5-\u0cb9\u0cbd\u0cde\u0ce0\u0ce1\u0d05-\u0d0c\u0d0e-\u0d10\u0d12-\u0d28\u0d2a-\u0d39\u0d60\u0d61\u0d85-\u0d96\u0d9a-\u0db1\u0db3-\u0dbb\u0dbd\u0dc0-\u0dc6\u0e01-\u0e30\u0e32\u0e33\u0e40-\u0e45\u0e81\u0e82\u0e84\u0e87\u0e88\u0e8a\u0e8d\u0e94-\u0e97\u0e99-\u0e9f\u0ea1-\u0ea3\u0ea5\u0ea7\u0eaa\u0eab\u0ead-\u0eb0\u0eb2\u0eb3\u0ebd\u0ec0-\u0ec4\u0edc\u0edd\u0f00\u0f40-\u0f47\u0f49-\u0f6a\u0f88-\u0f8b\u1000-\u1021\u1023-\u1027\u1029\u102a\u1050-\u1055\u10d0-\u10fa\u1100-\u1159\u115f-\u11a2\u11a8-\u11f9\u1200-\u1248\u124a-\u124d\u1250-\u1256\u1258\u125a-\u125d\u1260-\u1288\u128a-\u128d\u1290-\u12b0\u12b2-\u12b5\u12b8-\u12be\u12c0\u12c2-\u12c5\u12c8-\u12d6\u12d8-\u1310\u1312-\u1315\u1318-\u135a\u1380-\u138f\u13a0-\u13f4\u1401-\u166c\u166f-\u1676\u1681-\u169a\u16a0-\u16ea\u1700-\u170c\u170e-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176c\u176e-\u1770\u1780-\u17b3\u17dc\u1820-\u1842\u1844-\u1877\u1880-\u18a8\u1900-\u191c\u1950-\u196d\u1970-\u1974\u1980-\u19a9\u19c1-\u19c7\u1a00-\u1a16\u1b05-\u1b33\u1b45-\u1b4b\u2135-\u2138\u2d30-\u2d65\u2d80-\u2d96\u2da0-\u2da6\u2da8-\u2dae\u2db0-\u2db6\u2db8-\u2dbe\u2dc0-\u2dc6\u2dc8-\u2dce\u2dd0-\u2dd6\u2dd8-\u2dde\u3006\u303c\u3041-\u3096\u309f\u30a1-\u30fa\u30ff\u3105-\u312c\u3131-\u318e\u31a0-\u31b7\u31f0-\u31ff\u3400\u4db5\u4e00\u9fbb\ua000-\ua014\ua016-\ua48c\ua800\ua801\ua803-\ua805\ua807-\ua80a\ua80c-\ua822\ua840-\ua873\uac00\ud7a3\uf900-\ufa2d\ufa30-\ufa6a\ufa70-\ufad9\ufb1d\ufb1f-\ufb28\ufb2a-\ufb36\ufb38-\ufb3c\ufb3e\ufb40\ufb41\ufb43\ufb44\ufb46-\ufbb1\ufbd3-\ufd3d\ufd50-\ufd8f\ufd92-\ufdc7\ufdf0-\ufdfb\ufe70-\ufe74\ufe76-\ufefc\uff66-\uff6f\uff71-\uff9d\uffa0-\uffbe\uffc2-\uffc7\uffca-\uffcf\uffd2-\uffd7\uffda-\uffdc]',
		Co:'[\ue000\uf8ff]',
		Nd:'[\u0030-\u0039\u0660-\u0669\u06f0-\u06f9\u07c0-\u07c9\u0966-\u096f\u09e6-\u09ef\u0a66-\u0a6f\u0ae6-\u0aef\u0b66-\u0b6f\u0be6-\u0bef\u0c66-\u0c6f\u0ce6-\u0cef\u0d66-\u0d6f\u0e50-\u0e59\u0ed0-\u0ed9\u0f20-\u0f29\u1040-\u1049\u17e0-\u17e9\u1810-\u1819\u1946-\u194f\u19d0-\u19d9\u1b50-\u1b59\uff10-\uff19]',
		Lt:'[\u01c5\u01c8\u01cb\u01f2\u1f88-\u1f8f\u1f98-\u1f9f\u1fa8-\u1faf\u1fbc\u1fcc\u1ffc]',
		Lu:'[\u0041-\u005a\u00c0-\u00d6\u00d8-\u00de\u0100\u0102\u0104\u0106\u0108\u010a\u010c\u010e\u0110\u0112\u0114\u0116\u0118\u011a\u011c\u011e\u0120\u0122\u0124\u0126\u0128\u012a\u012c\u012e\u0130\u0132\u0134\u0136\u0139\u013b\u013d\u013f\u0141\u0143\u0145\u0147\u014a\u014c\u014e\u0150\u0152\u0154\u0156\u0158\u015a\u015c\u015e\u0160\u0162\u0164\u0166\u0168\u016a\u016c\u016e\u0170\u0172\u0174\u0176\u0178\u0179\u017b\u017d\u0181\u0182\u0184\u0186\u0187\u0189-\u018b\u018e-\u0191\u0193\u0194\u0196-\u0198\u019c\u019d\u019f\u01a0\u01a2\u01a4\u01a6\u01a7\u01a9\u01ac\u01ae\u01af\u01b1-\u01b3\u01b5\u01b7\u01b8\u01bc\u01c4\u01c7\u01ca\u01cd\u01cf\u01d1\u01d3\u01d5\u01d7\u01d9\u01db\u01de\u01e0\u01e2\u01e4\u01e6\u01e8\u01ea\u01ec\u01ee\u01f1\u01f4\u01f6-\u01f8\u01fa\u01fc\u01fe\u0200\u0202\u0204\u0206\u0208\u020a\u020c\u020e\u0210\u0212\u0214\u0216\u0218\u021a\u021c\u021e\u0220\u0222\u0224\u0226\u0228\u022a\u022c\u022e\u0230\u0232\u023a\u023b\u023d\u023e\u0241\u0243-\u0246\u0248\u024a\u024c\u024e\u0386\u0388-\u038a\u038c\u038e\u038f\u0391-\u03a1\u03a3-\u03ab\u03d2-\u03d4\u03d8\u03da\u03dc\u03de\u03e0\u03e2\u03e4\u03e6\u03e8\u03ea\u03ec\u03ee\u03f4\u03f7\u03f9\u03fa\u03fd-\u042f\u0460\u0462\u0464\u0466\u0468\u046a\u046c\u046e\u0470\u0472\u0474\u0476\u0478\u047a\u047c\u047e\u0480\u048a\u048c\u048e\u0490\u0492\u0494\u0496\u0498\u049a\u049c\u049e\u04a0\u04a2\u04a4\u04a6\u04a8\u04aa\u04ac\u04ae\u04b0\u04b2\u04b4\u04b6\u04b8\u04ba\u04bc\u04be\u04c0\u04c1\u04c3\u04c5\u04c7\u04c9\u04cb\u04cd\u04d0\u04d2\u04d4\u04d6\u04d8\u04da\u04dc\u04de\u04e0\u04e2\u04e4\u04e6\u04e8\u04ea\u04ec\u04ee\u04f0\u04f2\u04f4\u04f6\u04f8\u04fa\u04fc\u04fe\u0500\u0502\u0504\u0506\u0508\u050a\u050c\u050e\u0510\u0512\u0531-\u0556\u10a0-\u10c5\u1e00\u1e02\u1e04\u1e06\u1e08\u1e0a\u1e0c\u1e0e\u1e10\u1e12\u1e14\u1e16\u1e18\u1e1a\u1e1c\u1e1e\u1e20\u1e22\u1e24\u1e26\u1e28\u1e2a\u1e2c\u1e2e\u1e30\u1e32\u1e34\u1e36\u1e38\u1e3a\u1e3c\u1e3e\u1e40\u1e42\u1e44\u1e46\u1e48\u1e4a\u1e4c\u1e4e\u1e50\u1e52\u1e54\u1e56\u1e58\u1e5a\u1e5c\u1e5e\u1e60\u1e62\u1e64\u1e66\u1e68\u1e6a\u1e6c\u1e6e\u1e70\u1e72\u1e74\u1e76\u1e78\u1e7a\u1e7c\u1e7e\u1e80\u1e82\u1e84\u1e86\u1e88\u1e8a\u1e8c\u1e8e\u1e90\u1e92\u1e94\u1ea0\u1ea2\u1ea4\u1ea6\u1ea8\u1eaa\u1eac\u1eae\u1eb0\u1eb2\u1eb4\u1eb6\u1eb8\u1eba\u1ebc\u1ebe\u1ec0\u1ec2\u1ec4\u1ec6\u1ec8\u1eca\u1ecc\u1ece\u1ed0\u1ed2\u1ed4\u1ed6\u1ed8\u1eda\u1edc\u1ede\u1ee0\u1ee2\u1ee4\u1ee6\u1ee8\u1eea\u1eec\u1eee\u1ef0\u1ef2\u1ef4\u1ef6\u1ef8\u1f08-\u1f0f\u1f18-\u1f1d\u1f28-\u1f2f\u1f38-\u1f3f\u1f48-\u1f4d\u1f59\u1f5b\u1f5d\u1f5f\u1f68-\u1f6f\u1fb8-\u1fbb\u1fc8-\u1fcb\u1fd8-\u1fdb\u1fe8-\u1fec\u1ff8-\u1ffb\u2102\u2107\u210b-\u210d\u2110-\u2112\u2115\u2119-\u211d\u2124\u2126\u2128\u212a-\u212d\u2130-\u2133\u213e\u213f\u2145\u2183\u2c00-\u2c2e\u2c60\u2c62-\u2c64\u2c67\u2c69\u2c6b\u2c75\u2c80\u2c82\u2c84\u2c86\u2c88\u2c8a\u2c8c\u2c8e\u2c90\u2c92\u2c94\u2c96\u2c98\u2c9a\u2c9c\u2c9e\u2ca0\u2ca2\u2ca4\u2ca6\u2ca8\u2caa\u2cac\u2cae\u2cb0\u2cb2\u2cb4\u2cb6\u2cb8\u2cba\u2cbc\u2cbe\u2cc0\u2cc2\u2cc4\u2cc6\u2cc8\u2cca\u2ccc\u2cce\u2cd0\u2cd2\u2cd4\u2cd6\u2cd8\u2cda\u2cdc\u2cde\u2ce0\u2ce2\uff21-\uff3a]',
		Cs:'[\ud800\udb7f\udb80\udbff\udc00\udfff]',
		Zl:'[\u2028]',
		Nl:'[\u16ee-\u16f0\u2160-\u2182\u3007\u3021-\u3029\u3038-\u303a]',
		Zp:'[\u2029]',
		No:'[\u00b2\u00b3\u00b9\u00bc-\u00be\u09f4-\u09f9\u0bf0-\u0bf2\u0f2a-\u0f33\u1369-\u137c\u17f0-\u17f9\u2070\u2074-\u2079\u2080-\u2089\u2153-\u215f\u2460-\u249b\u24ea-\u24ff\u2776-\u2793\u2cfd\u3192-\u3195\u3220-\u3229\u3251-\u325f\u3280-\u3289\u32b1-\u32bf]',
		Zs:'[\u0020\u00a0\u1680\u180e\u2000-\u200a\u202f\u205f\u3000]',
		Sc:'[\u0024\u00a2-\u00a5\u060b\u09f2\u09f3\u0af1\u0bf9\u0e3f\u17db\u20a0-\u20b5\ufdfc\ufe69\uff04\uffe0\uffe1\uffe5\uffe6]',
		Pc:'[\u005f\u203f\u2040\u2054\ufe33\ufe34\ufe4d-\ufe4f\uff3f]',
		Pd:'[\u002d\u058a\u1806\u2010-\u2015\u2e17\u301c\u3030\u30a0\ufe31\ufe32\ufe58\ufe63\uff0d]',
		Pe:'[\u0029\u005d\u007d\u0f3b\u0f3d\u169c\u2046\u207e\u208e\u232a\u2769\u276b\u276d\u276f\u2771\u2773\u2775\u27c6\u27e7\u27e9\u27eb\u2984\u2986\u2988\u298a\u298c\u298e\u2990\u2992\u2994\u2996\u2998\u29d9\u29db\u29fd\u3009\u300b\u300d\u300f\u3011\u3015\u3017\u3019\u301b\u301e\u301f\ufd3f\ufe18\ufe36\ufe38\ufe3a\ufe3c\ufe3e\ufe40\ufe42\ufe44\ufe48\ufe5a\ufe5c\ufe5e\uff09\uff3d\uff5d\uff60\uff63]',
		Pf:'[\u00bb\u2019\u201d\u203a\u2e03\u2e05\u2e0a\u2e0d\u2e1d]',
		Me:'[\u0488\u0489\u06de\u20dd-\u20e0\u20e2-\u20e4]',
		Mc:'[\u0903\u093e-\u0940\u0949-\u094c\u0982\u0983\u09be-\u09c0\u09c7\u09c8\u09cb\u09cc\u09d7\u0a03\u0a3e-\u0a40\u0a83\u0abe-\u0ac0\u0ac9\u0acb\u0acc\u0b02\u0b03\u0b3e\u0b40\u0b47\u0b48\u0b4b\u0b4c\u0b57\u0bbe\u0bbf\u0bc1\u0bc2\u0bc6-\u0bc8\u0bca-\u0bcc\u0bd7\u0c01-\u0c03\u0c41-\u0c44\u0c82\u0c83\u0cbe\u0cc0-\u0cc4\u0cc7\u0cc8\u0cca\u0ccb\u0cd5\u0cd6\u0d02\u0d03\u0d3e-\u0d40\u0d46-\u0d48\u0d4a-\u0d4c\u0d57\u0d82\u0d83\u0dcf-\u0dd1\u0dd8-\u0ddf\u0df2\u0df3\u0f3e\u0f3f\u0f7f\u102c\u1031\u1038\u1056\u1057\u17b6\u17be-\u17c5\u17c7\u17c8\u1923-\u1926\u1929-\u192b\u1930\u1931\u1933-\u1938\u19b0-\u19c0\u19c8\u19c9\u1a19-\u1a1b\u1b04\u1b35\u1b3b\u1b3d-\u1b41\u1b43\u1b44\ua802\ua823\ua824\ua827]'
	};
	/* Also supports the general category (only the first letter) */
	var firstLetters = {};
	for (var p in unicodeCategories)
	{
		if (firstLetters[p[0]])
			firstLetters[p[0]] = unicodeCategories[p].substring(0,unicodeCategories[p].length-1) + firstLetters[p[0]].substring(1);
		else
			firstLetters[p[0]] = unicodeCategories[p];
	}
	for (var p in firstLetters)
		unicodeCategories[p] = firstLetters[p];

	/* Gets a regex written in a dialect that supports unicode categories and
	   translates it to a dialect supported by JavaScript. */
	return function(regexpString, classes)
	{
		var modifiers = "";
		if ( regexpString instanceof RegExp ) {
			modifiers = (regexpString.global ? "g" : "") +
						(regexpString.ignoreCase ? "i" : "") +
						(regexpString.multiline ? "m" : "");
			regexpString = regexpString.source;
		}
		regexpString = regexpString.replace(/\\p\{(..?)\}/g, function(match,group) {
		var unicode_categorie = unicodeCategories[group];
		if (!classes)
			unicode_category = unicode_categorie.replace(/\[(.*?)\]/g,"$1")
			return unicode_category || match;
		});
		return new RegExp(regexpString,modifiers);
	};

})();
var eamLineChart, eamLineChartCtx, eamLineChartReward, eamLineChartRewardData, checkAmountXhr, eamProductChart,
    eamProductChartData;
var eamChartPieReward;
var eamChartPieRewardData;
var eamLineChartTmp = [];
$(document).ready(function () {
    $('.ets-am-content').find('input').attr('autocomplete','off');
    $(document).on('change','.type_date_filter',function(){
       if($(this).val()=='time_ranger')
       {
           if($(this).next('.box-date-ranger').length)
           {
               $(this).next('.box-date-ranger').addClass('show');
           }
       }
       else {
           if($(this).next('.box-date-ranger').length)
           {
               $(this).next('.box-date-ranger').removeClass('show');
           }
       }
    });
    $(document).mouseup(function (e)
    {
        var container_pop_table=$('.aff-product-popup-share-mail');
        if (!container_pop_table.is(e.target)&& container_pop_table.has(e.target).length === 0)
        {
            $('.aff-product-popup-share-mail').removeClass('show');
        }
        if (!$('.aff-product-share-frontend').is(e.target)&& $('.aff-product-share-frontend').has(e.target).length === 0)
        {
            if(!$('.aff-product-share-list').hasClass('product-page'))
                $('.aff-product-share-list').hide();
        }
    });
    $(document).on('click','button[name="affSubmitSharEmail"]',function(e){
        e.preventDefault();
        if(!$(this).hasClass('loading'))
        {
            $('.aff-product-popup-share-mail .module_error').parent().remove();
            $('.aff-product-popup-share-mail .alert-success').parent().remove();
            $(this).addClass('loading');
            var url_ajax = $(this).data('link');
            $.ajax({
                url: url_ajax,
                type: 'POST',
                dataType: 'json',
                data: {
                    affSubmitSharEmail: 1,
                    aff_mails: $('input[name="aff-emails"]').val(),
                    aff_name: $('input[name="aff-name"]').val(),
                    aff_message: $('textarea[name="aff-messages"]').val(),
                    aff_product_share_link :$('#aff-product-share-link').val(),
                    aff_product_share_name :$('#aff-product-share-name').val(),
                    token:eam_token,
                },
                success: function (json) {
                    if(json.errors)
                    {
                        $('.aff-product-popup-share-mail .form-wrapper').append(json.errors);
                    }
                    if(json.success)
                    {
                        $('.aff-product-popup-share-mail .form-wrapper').append(json.success);
                    }
                    $('button[name="affSubmitSharEmail"]').removeClass('loading');
                }
            }); 
        }
        
    });

    $(document).on('click','.aff-product-share',function(){
        $(this).next('.aff-product-share-list').toggle(); 
    });

    $(document).on('click','.aff-product-share-email',function(e){
       e.preventDefault();
       var link_share = $(this).attr('href');
       $('#aff-product-share-link').val(link_share);
       $('#aff-product-share-name').val($(this).data('product-name'));
       $('.aff-product-popup-share-mail').addClass('show');
    });
    $(document).on('click','.aff-close',function(){
        $('.aff-product-popup-share-mail').removeClass('show');
    });
    $('.eam-tooltip-bs').tooltip();
    $(document).on('click', '#eam-affiliate-link-add-on', function (e) {
        e.preventDefault();
        var copyButton = $(this);
        var input = copyButton.closest('.input-group').find('input');
        var value = input.val();
        if (eamCopyToClipboard(value)) {
            var origin = copyButton.attr('data-eam-tooltip');
            var success = copyButton.attr('data-eam-copy');
            copyButton.attr('data-eam-tooltip', success);
            setTimeout(function () {
                copyButton.attr('data-eam-tooltip', origin);
            }, 2000);
        }
    });

    $(document).on('click', '.eam-input-link', function (e) {
        $(this).select();
        var input = $(this);
        var value = input.val();
        var copyButton = input.parent('.input-group').find('span.input-group-addon.eam-tooltip');
        if (eamCopyToClipboard(value, this)) {
            var origin = copyButton.attr('data-eam-tooltip');
            var success = copyButton.attr('data-eam-copy');
            copyButton.attr('data-eam-tooltip', success);
            setTimeout(function () {
                copyButton.attr('data-eam-tooltip', origin);
            }, 2000);
        }
    });
    $(document).on('focus', '.eam-input-link', function(event) {
        event.preventDefault();
        $(this).select();
    });
    if ($('body[id=cart]').length > 0 && link_cart && link_reward) {
        $(document).ajaxComplete(function (event, xhr, settings) {
            if (settings.url.indexOf(link_cart) !== -1) {
                $('#ets_affiliatemarketing_cart_message').addClass('loading');
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: link_reward,
                    data: {ajax: 1, token: eam_token},
                    success: function (data) {
                        $('#ets_affiliatemarketing_cart_message').removeClass('loading');
                        var htmlRenderer = '';
                        if (data.message) {
                            htmlRenderer += '<div class="alert alert-info">' + data.message + '</div>'
                        }
                        $('#ets_affiliatemarketing_cart_message').empty().html(htmlRenderer);
                    }
                });
            }
        });
    }
    if ($('.eam-input-file').length > 0) {
        var eam_fileInput = document.querySelector(".eam-input-file"),
            eam_button = document.querySelector(".eam-input-file-trigger"),
            eam_the_return = document.querySelector(".eam-file-return");
        eam_button.addEventListener("keydown", function (event) {
            if (event.keyCode == 13 || event.keyCode == 32) {
                eam_fileInput.focus();
            }
        });
        eam_button.addEventListener("click", function (event) {
            eam_fileInput.focus();
            return false;
        });
        eam_fileInput.addEventListener("change", function (event) {
            var path_img_arr = this.value.split('\\');
            if (path_img_arr[path_img_arr.length - 1]) {
                eam_the_return.innerHTML = path_img_arr[path_img_arr.length - 1];
            }

        });
        $('.eam-input-file-trigger').on("click", function () {
            $('#eam_ref_banner').trigger('click');
        })
    }
    if ($('#eam-input-upload-invoice').length > 0) {
        var eam_fileInput = document.querySelector("#eam-input-upload-invoice"),
            eam_button = document.querySelector(".eam-input-upload-invoice-trigger"),
            eam_the_return = document.querySelector(".eam-file-upload-invoice-return");
        eam_button.addEventListener("keydown", function (event) {
            if (event.keyCode == 13 || event.keyCode == 32) {
                eam_fileInput.focus();
            }
        });
        eam_button.addEventListener("click", function (event) {
            eam_fileInput.focus();
            return false;
        });
        eam_fileInput.addEventListener("change", function (event) {
            var path_img_arr = this.value.split('\\');
            if(path_img_arr[path_img_arr.length - 1]) {
                eam_the_return.innerHTML = path_img_arr[path_img_arr.length - 1];
            }
        });
    }
    $('.stats-loading').hide();
    eamLineChartCtx = document.getElementById('eam-stats-programs');
    $('.ets-am-program .js-add-mail-inviting-ref').click(function (event) {
        if ($('.ets-am-program .mail-inviting-item').length < 10) {
            mail_item = $('.ets-am-program .mail-inviting-item').last().clone().html();
            mail_order = $('.ets-am-program .mail-inviting-item').last().find('.js-order-number').first().html();

            mail_item = mail_item.replace('<span class="js-order-number">' + mail_order + '</span>', '<span class="js-order-number">' + (parseInt(mail_order) + 1) + '</span>');
            mail_item = mail_item.replace('invite_email_' + mail_order, 'invite_email_' + (parseInt(mail_order) + 1));
            mail_item = mail_item.replace('invite_name_' + mail_order, 'invite_name_' + (parseInt(mail_order) + 1));
            $('.ets-am-program .mail-inviting-item').last().after('<div class="mail-inviting-item mail-inviting-item-added"><span class="remove-mail-inviting-item">&times;</span>' + mail_item + '</div>');
        } else {
            $('.js-add-mail-inviting-ref').hide();
        }
    });
    $('.ets-am-program ').on('click', '.remove-mail-inviting-item', function (event) {
        event.preventDefault();
        $(this).parent().remove();
    });
    $('.ets-am-program input[type=email]').focusout(function (event) {
        if (!eamValidateEmail($(this).val())) {
            $(this).closest('.form-group').find('.error').remove();
            if (typeof eam_email_invalid !== 'undefined') {
                error_html = '<div class="error">' + eam_email_invalid + '</div>';
                $(this).closest('.form-group').append(error_html);
            } else {
                $(this).closest('.form-group').find('.error').remove();
            }
            $('.ets-am-program .js-send-email-inviting').prop('disabled', true);
        }
    });
    $('.ets-am-program input[type=email]').focus(function (event) {
        $('.ets-am-program .js-send-email-inviting').prop('disabled', false);
    });
    $('.ets-am-program .js-send-email-inviting').click(function (event) {
        button_html = $(this).html();
        $this = $(this);
        var error = false;
        $('.error').remove();
        if($('form.eam-form-email-inviting input[name=name]').val()=='')
        {
            $('.eam-form-email-inviting input[name="name"]').after('<p class="error">'+name_is_blank+'</p>');
            error = true;
        }
        if($('form.eam-form-email-inviting input[name=email]').val()=='')
        {
            $('form.eam-form-email-inviting input[name=email]').after('<p class="error">'+email_is_blank+'</p>');
            error = true;
        }
        else if(!eamIsEmail($('form.eam-form-email-inviting input[name=email]').val()))
        {
            $('form.eam-form-email-inviting input[name=email]').after('<p class="error">'+email_is_invalid+'</p>');
            error = true;
        }
        if(!error)
        {
            var mail_datas = {
                name: $('form.eam-form-email-inviting input[name=name]').val(),
                email: $('form.eam-form-email-inviting input[name=email]').val()
            };
            $.ajax({
                url: '',
                type: 'POST',
                data: {
                    send_mail_invite: true,
                    mails: mail_datas,
                    token: eam_token
                },
                beforeSend: function () {
                    if (typeof eam_sending_email !== 'undefined') {
                        $this.html(eam_sending_email);
                    }
                    $this.prop('disabled', true);
                },
                success: function (res) {
                    if (typeof res !== 'object') {
                        res = JSON.parse(res);
                    }
                    if (res.success) {
                        $('.eam-alert-sent-mail').html('<div class="alert alert-success">'+res.message+'</div>');
                        if (typeof res.limited !== 'undefined' && res.limited) {
                            limited_html = '<div class="alert alert-info">';
                            limited_html = '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                            limited_html += eam_email_sent_limited;
                            limited_html += '</div>';
                            $('form.eam-form-email-inviting').after(limited_html);
                            $('form.eam-form-email-inviting').remove();
                        }
                        location.reload();
                    } else {
                        $('.eam-alert-sent-mail').html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+res.message+'</div>');
                    }
                },
                complete: function () {
                    $this.html(button_html);
                    $this.prop('disabled', false);
                },
                error: function (xhr) {
                }
            })
        }
        
    });

    $('.ets-am-program .js-upload-your-banner').click(function (event) {
        file_banner = $('#eam_ref_banner').prop('files')[0];
        formData = new FormData();
        formData.append('banner', file_banner);
        formData.append('upload_banner', true);
        formData.append('token', eam_token);
        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (res) {
                if (typeof res !== 'object') {
                    res = JSON.parse(res);
                }
                if (res.success) {
                    eamShowAlert(res.message, 'success');
                    img_banner = '<img src="' + res.img + '?v=' + (Math.random()) + '" class="img-preview"><span class="delete-banner" title="' + eam_trans['delete'] + '"><i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></i></span>';
                    $('#eam_ref_banner').closest('form').find('.preview-banner').html(img_banner);
                    $('#eam-ref-banner-code').text(res.embed_code);
                    $('.ets-am-program .delete-banner').show();
                } else {
                    eamShowAlert(res.message, 'danger');
                }
            }
        });
    });

    $(document).on('click', '.ets-am-program .delete-banner', function (event) {
        event.preventDefault();
        if (!confirm(eam_trans['confirm_delete_banner'])) {
            return false;
        }
        $.ajax({
            url: '',
            type: 'POST',
            data: {delete_banner: true, token: eam_token},
            success: function (res) {
                if (typeof res !== 'undefined') {
                    res = JSON.parse(res);
                }
                if (res.success) {
                    eamShowAlert(res.message, 'success');
                    img_banner = '';
                    if (res.img) {
                        img_banner = '<img src="' + res.img + '?v=' + (Math.random()) + '" class="img-preview"><span class="delete-banner" title="' + eam_trans['delete'] + '"><i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></i></span>';
                    }
                    $('#eam_ref_banner').closest('form').find('.preview-banner').html(img_banner);
                    $('#eam-ref-banner-code').text(res.embed_code);
                    $('.ets-am-program .delete-banner').hide();
                } else {
                    eamShowAlert(res.message, 'danger');
                }
            },
            error: function (xhr) {

            }
        });
    });

    var eamRefPopup = document.getElementById('ets-am-ref-popup');
    var eamRefPopupVoucher = document.getElementById('ets-am-ref-popup-voucher');
    var eamRefPopupClose = document.getElementsByClassName("ets-am-ref-popup-close")[0];
    var eamRefPopupClose2 = document.getElementsByClassName("js-eam-close-popup-ref")[0];
    var eamAffPopupVoucher = document.getElementById('ets_am_aff_modal_promo_code');

    if (typeof eamRefPopupClose !== 'undefined') {
        eamRefPopupClose.onclick = function () {
            eamRefPopup.style.display = "none";
        }
    }
    if (typeof eamRefPopupClose2 !== 'undefined') {
        eamRefPopupClose2.onclick = function () {
            eamRefPopup.style.display = "none";
        }
    }
    window.onclick = function (event) {
        if (event.target == eamRefPopup) {
            eamRefPopup.style.display = "none";
        }
        if (event.target == eamRefPopupVoucher) {
            eamRefPopupVoucher.style.display = "none";
        }

        if (event.target == eamAffPopupVoucher) {
            eamAffPopupVoucher.style.display = "none";
        }
    }

    if (typeof eam_show_popup_ref !== 'undefined' && typeof eam_link_ajax_exec !== 'undefined') {
        eam_show_popup_ref = parseInt(eam_show_popup_ref);
        if (eam_show_popup_ref) {
            setTimeout(function () {
                eamRefPopup.style.display = 'block';
                $.ajax({
                    url: eam_link_ajax_exec,
                    type: 'POST',
                    data: {
                        save_cookie_ref_popup: true,
                        token: eam_token
                    },
                    success: function (res) {
                    }
                })
            }, 3000);
        }
    }

    $('#ets-am-ref-popup-voucher .ets-am-ref-popup-voucher-close').click(function (event) {
        $('#ets-am-ref-popup-voucher').hide();
    });
    $(document).keyup(function(e) { 
        if(e.keyCode == 27 ) {
            $('#ets_am_aff_modal_promo_code').hide();
            $('.aff-product-popup-share-mail').removeClass('show');
        }
    });
    $('#ets_am_aff_modal_promo_code .ets-am-ref-popup-voucher-close,.shop_now_aff').click(function (event) {
        $('#ets_am_aff_modal_promo_code').hide();
    });
    var eamDate = new Date();
    if (typeof moment !== 'undefined') {
        $('.eam_date_ranger_filter').daterangepicker({
            startDate: moment(new Date(eamDate.getFullYear(), eamDate.getMonth(), 1)),
            endDate: moment(new Date(eamDate.getFullYear(), eamDate.getMonth() + 1, 0)),
            showDropdowns:true,
            locale: {
                format: 'YYYY/MM/DD'
            }

        });
    }

    eamSetDateFilterStat('.eam_date_ranger_filter','/','-');
    $('.eam_date_ranger_filter').on('apply.daterangepicker', function (ev, picker) {
        eamSetDateFilterStat('.eam_date_ranger_filter','/','-');
        eamLoadStats();
    });
    $('.eam-statistic-reward .filter_status_reward').change(function (event) {
        eamLoadStats();
    });
    $('.eam-statistic-reward .js-btn-reset-filter').click(function (event) {
        $('#eam_data_filter_stat_reward input[type=text], #eam_data_filter_stat_reward input[type=hidden]').val('');
        $('#eam_data_filter_stat_reward input[type=checkbox]').prop('checked', false);
        eamLoadStats();
    });
    //Get stat data
    eamLoadStats();
    eam_url_vars = eamGetUrlVars();
    eam_data_stats_req = {
        get_stat_reward: true,
        filter_type_stats: 'reward'
    };
    if (typeof eam_data_stats !== 'undefined') {
        if (typeof eam_data_stats !== 'object') {
            eam_data_stats = JSON.parse(eam_data_stats);
        }
        var init_define_columns = [];
        $.each(eam_data_stats.data[0].values, function(index, el) {
            init_define_columns.push(el.x);
        });
        nv.addGraph(function () {
            eamLineChartReward = nv.models.lineChart()
                .margin({left: 80})
                .useInteractiveGuideline(true)
                .transitionDuration(350)
                .showLegend(true)
                .showYAxis(true)
                .showXAxis(true);
            var axisLabel = eam_data_stats.x_asis == 'date'? eam_chart_day : (eam_data_stats.x_asis == 'month'? eam_chart_month : eam_chart_year);
            eamLineChartReward.xAxis
                .tickValues(init_define_columns)
                .axisLabel(axisLabel)
                .rotateLabels(eam_data_stats.count_values > 31? -45 : 0)
                .tickFormat(function (d) {
                    if (eamLineChartTmp.indexOf(d) < 0) {
                        eamLineChartTmp.push(d);
                    } else {
                        return;
                    }
                    if (eam_data_stats.x_asis == 'date') {
                        return parseInt(d3.time.format('%d')(new Date(d)));
                    } else if (eam_data_stats.x_asis == 'month') {
                        return parseInt(d3.time.format('%m')(new Date(d)));
                    } else if (eam_data_stats.x_asis == 'year') {
                        return d3.time.format('%Y')(new Date(d))
                    }
                    return d;
                });
            eamLineChartReward.yAxis
                .axisLabel(eam_chart_currency_code)
                .tickFormat(d3.format('.02f'));
            eamLineChartRewardData = d3.select('#eam_stats_reward_line svg')
                .datum(eam_data_stats.data)
                .call(eamLineChartReward);
            nv.utils.windowResize(eamLineChartReward.update);
            return eamLineChartReward;
        });
    }
    if (typeof ets_am_product_stats !== 'undefined') {
        var init_define_columns2 = [];
        $.each(ets_am_product_stats.data[0].values, function(index, el) {
            init_define_columns2.push(el.x);
        });
        nv.addGraph(function () {
            eamProductChart = nv.models.lineChart()
                .margin({left: 60, bottom: 80})
                .useInteractiveGuideline(true)
                .transitionDuration(350)
                .showLegend(true)
                .showYAxis(true)
                .showXAxis(true);
            var axisLabel = ets_am_product_stats.x_asis == 'date'? eam_chart_day : (ets_am_product_stats.x_asis == 'month'? eam_chart_month : eam_chart_year);
            eamProductChart.xAxis
                .tickValues(init_define_columns2)
                .axisLabel(axisLabel)
                .rotateLabels(-45)
                .tickFormat(function (d) {
                    if (eamLineChartTmp.indexOf(d) < 0) {
                        eamLineChartTmp.push(d);
                    } else {
                        return;
                    }
                    if (ets_am_product_stats.x_asis == 'date') {
                        return parseInt(d3.time.format('%d')(new Date(d)));
                    } else if (ets_am_product_stats.x_asis == 'month') {
                        return parseInt(d3.time.format('%m')(new Date(d)));
                    } else if (ets_am_product_stats.x_asis == 'year') {
                        return d3.time.format('%Y')(new Date(d))
                    }
                    return d;
                });
            eamProductChart.yAxis
                .axisLabel(eam_chart_currency_code)
                .tickFormat(d3.format('.02f'));
            eamProductChartData = d3.select('#eam_aff_stats svg')
                .datum(ets_am_product_stats.data)
                .call(eamProductChart);
            nv.utils.windowResize(eamProductChart.update);
            return eamProductChart;
        });
    }
    /* ====== PIE CHART ======*/
    if(typeof eam_data_pie_chart !== 'undefined'){
        if(typeof eam_data_pie_chart !== 'object'){
            eam_data_pie_chart = JSON.parse(eam_data_pie_chart);
        }
        nv.addGraph(function() {
          eamChartPieReward = nv.models.pieChart()
              .x(function(d) { return d.label })
              .y(function(d) { return d.value })
              .margin({left: 0, right: 0, top : 25, bottom : 0})
              .showLabels(true)
              .labelThreshold(.05)
              .pieLabelsOutside(false)
              .labelType("percent");
            eamChartPieReward.noData('No Data found');
            eamChartPieRewardData = d3.select("#eam_stats_reward_pie svg")
                .datum(eam_data_pie_chart)
                .transition().duration(350)
                .call(eamChartPieReward);
            nv.utils.windowResize(eamChartPieReward.update);
            eamChartPieReward.tooltipContent(function(key, y, e, graph) {
                return '<h3>' + key + '</h3>' +
                       '<p>' +  y + ' '+eam_chart_currency_code+ '</p>';
            });
            eamChartPieReward.update();
          return eamChartPieReward;
        });
    }
    

    /* ====== END PIE CHART ======*/
    if ($('.stats-data-reward button[data-type=time_ranger]').hasClass('active')) {
        $('.stats-data-reward .box-date-ranger').addClass('show');
    } else {
        $('.stats-data-reward .box-date-ranger').removeClass('show');
    }
    $('.eam_date_ranger_filter').on('apply.daterangepicker', function (ev, picker) {
        $(this).closest('.box-date-ranger').find('.date_from_reward').val(picker.startDate.format('YYYY-MM-DD'));
        $(this).closest('.box-date-ranger').find('.date_to_reward').val(picker.endDate.format('YYYY-MM-DD'));
    });
    $('.eam_date_ranger_filter').on('hide.daterangepicker', function (ev, picker) {
        $(this).closest('.box-date-ranger').find('.date_from_reward').val(picker.startDate.format('YYYY-MM-DD'));
        $(this).closest('.box-date-ranger').find('.date_to_reward').val(picker.endDate.format('YYYY-MM-DD'));
    });
    $('.eam-dashboard .js-type-info-stats').click(function (event) {
        var type_stats = $(this).attr('data-type');
        var bg_stats = $(this).attr('data-bg');
        if (type_stats) {
            $('.eam-dashboard input[name=type_stats]').val(type_stats);
            $('.eam-dashboard .js-type-info-stats').each(function (index, el) {
                $(el).find('.box-inner').removeClass($(el).attr('data-bg'));
            });
            $(this).find('.box-inner').addClass(bg_stats);
            $('.stats-data-reward .js-btn-submit-filter').click();
        }
    });
    $('.stats-data-reward .js-btn-submit-filter').click(function (event) {
        from = $('.stats-data-reward .date_from_reward').val();
        to = $('.stats-data-reward .date_to_reward').val();
        if (from && to) {
            regex_date = /^\d{4}\-\d{1,2}\-\d{1,2}$/;
            if (!regex_date.test(from) || !regex_date.test(to)) {
                showErrorMessage('The format of date is invalid');
                return false;
            }
        }
        if ($('#eam_stats_reward_line').length > 0 || $('#eam_aff_stats').length > 0) {
            eam_data_stats_req = {
                get_stat_reward: true,
                tab_active: eam_url_vars.tabActive !== 'undefined' ? eam_url_vars.tabActive : '',
                program: $('.stats-data-reward select[name=program]').val()
            };
            eam_data_stats_req.filter_date_type = $('.stats-data-reward select[name=type_date_filter]').val();
            if ($('.stats-data-reward select[name=type_date_filter]').val() == 'time_ranger') {
                eam_data_stats_req.filter_date_from = from;
                eam_data_stats_req.filter_date_to = to;
            }
            eam_data_stats_req.filter_status = [];
            eam_data_stats_req.filter_type_stats = $('.eam-dashboard input[name=type_stats]').val();
            eam_data_stats_req.filter_status.push($('.stats-data-reward select[name=status]').val());
            eamAjaxChartStat(eam_data_stats_req);
            
        }
    });

    $('.stats-data-reward .js-btn-reset-filter').click(function (event) {
        $('.stats-data-reward .stat-filter input[type=text]').val('');
        $('.stats-data-reward .stat-filter select option:first-child').prop('selected', 'selected');
        $(this).closest('form').submit();
        if ($('#eam_stats_reward_line').length > 0) {
            eam_data_stats_req = {
                get_stat_reward: true,
                tab_active: eam_url_vars.tabActive !== 'undefined' ? eam_url_vars.tabActive : '',
            };
            eam_data_stats_req_pie = {
                get_pie_reward: true,
                tab_active: eam_url_vars.tabActive !== 'undefined' ? eam_url_vars.tabActive : '',
            };
            eamAjaxChartStat(eam_data_stats_req);
        }

    });
    $('.eam-box-filter .js-btn-reset-form-filter').click(function (event) {
        $('.eam-box-filter input[type=text]').val('');
        $('.eam-box-filter select').val('');
    });

    $('.stats-data-reward select[name=type_date_filter]').change(function (event) {
        if ($(this).val() == 'time_ranger') {
            $(this).next('.box-date-ranger').addClass('show');
        } else {
            $(this).next('.box-date-ranger').removeClass('show');
        }
    });

    $('.eam-box-filter select[name="type_date_filter"]').change(function (event) {
        if ($(this).val() == 'time_ranger') {
            $(this).next('.box-date-ranger').addClass('show');
        } else {
            $(this).next('.box-date-ranger').removeClass('show');
        }
    });

    $('.eam_select_filter select[name=type_date_filter]').change(function () {
        if ($(this).val() === 'time_ranger') {
            $(this).closest('.eam_select_filter').find('.box-date-ranger').addClass('show');
        } else {
            $(this).closest('.eam_select_filter').find('.box-date-ranger').removeClass('show');
        }
    });

    if ($('.eam-box-filter select[name=type_date_filter]').val() === 'time_ranger') {
        $('.eam-box-filter select[name=type_date_filter]').closest('.eam_select_filter').find('.box-date-ranger').addClass('show');
    } else {
        $('.eam-box-filter select[name=type_date_filter]').closest('.eam_select_filter').find('.box-date-ranger').removeClass('show');
    }

    if ($('.stats-data-reward select[name=type_date_filter]').val() == 'time_ranger') {
        $('.stats-data-reward select[name=type_date_filter]').next('.box-date-ranger').show();
    } else {
        $('.stats-data-reward select[name=type_date_filter]').next('.box-date-ranger').removeClass('show');
    }

    if ($('.eam_select_filter select.type_date_filter').val() == 'time_ranger') {
        $('.eam-box-filter select.type_date_filter').next('.box-date-ranger').addClass('show');
    } else {
        $('.eam-box-filter select.type_date_filter').next('.box-date-ranger').removeClass('show');
    }

    if(!$('.eam-withdraw-form input[name=EAM_AMOUNT_WITHDRAW]').val() || !eamIsFloat($('.eam-withdraw-form input[name=EAM_AMOUNT_WITHDRAW]').val())){
        $('.eam-withdraw-form').find('button.eam-submit-request').prop('disabled', true);
    }
    else{
        $('.eam-withdraw-form').find('button.eam-submit-request').prop('disabled', false);
    }
    $('.eam-withdraw-form input[name=EAM_AMOUNT_WITHDRAW]').keyup(function(event) {
        if(!$(this).val() || !eamIsFloat($(this).val())){
            $('.eam-withdraw-form').find('button.eam-submit-request').prop('disabled', true);
        }
        else{
            $('.eam-withdraw-form').find('button.eam-submit-request').prop('disabled', false);
        }
    });
    var withdrawForm = $('.eam-withdraw-form');
    var submit = withdrawForm.find('button.eam-submit-request');

    submit.on('click', function (e) {
        e.preventDefault();
        if ($.trim($('input[name=EAM_AMOUNT_WITHDRAW]').val()) && eamIsFloat($.trim($('input[name=EAM_AMOUNT_WITHDRAW]').val()))){
            var message = eam_confirmation_withdraw;
            message = message.replace(':%d', $.trim($('input[name=EAM_AMOUNT_WITHDRAW]').val()));
            if (confirm(message)) {
                withdrawForm.submit();
            }
        }
    });
    var default_amount = $('input[name=EAM_AMOUNT_WITHDRAW]').val();
    if ($.trim(default_amount) !== '') {
        eamCheckAmount($('input[name=EAM_AMOUNT_WITHDRAW]'));
    }
    $('input[name=EAM_AMOUNT_WITHDRAW]').on('change', function () {
        eamCheckAmount($(this))
    });
    $('[data-toggle="tooltip"]').tooltip();
    $('.eam-pagination a').click(function (event) {
        var query_url = window.location.href;
        var _page = $(this).attr('data-page');
        query_url = eamUpdateQueryStringParameter(query_url, 'page', _page);
        window.location.href = query_url;
    });
    var convertVoucherForm = $('.eam-voucher-form  form');
    var submitVoucher = $('.eam-voucher-form button[type=submit]');
    submitVoucher.on('click', function (e) {
        e.preventDefault();
        if (confirm(eam_confirm_convert_voucher)) {
            convertVoucherForm.submit();
        }
    });
    $('.eam-box-filter .js-btn-reset-filter').click(function (event) {
        $(this).closest('form').find('input[type=text]').val('');
        $(this).closest('form').find('select option:first-child').prop('selected', 'selected');
        var form_data = $(this).closest('form').serializeArray();
        var query_url = window.location.href;
        $.each(form_data, function (index, el) {
            query_url = eamUpdateQueryStringParameter(query_url, el.name, el.value);
        });
        window.location.href = query_url;
    });

    $('.js-eam-btn-copylink').click(function (event) {
        $this = $(this);
        var $this = $(this);
        var input = $this.closest('.input-group').find('input');
        var value = input.val();
        if (eamCopyToClipboard(value,null)) {
            var origin = $this.attr('data-eam-tooltip');
            var success = $this.attr('data-eam-copy');
            $this.attr('data-eam-tooltip', success);
            setTimeout(function () {
                $this.attr('data-eam-tooltip', origin);
            }, 2000);
        }
    });
    $('.js-eam-order-data').click(function (event) {
        /* Act on the event */
        var order_by = $(this).attr('data-order-by');
        var order_way = $(this).attr('data-order-way');
        if (order_by && order_way) {
            var query_url = window.location.href;
            query_url = eamUpdateQueryStringParameter(query_url, 'orderBy', order_by);
            query_url = eamUpdateQueryStringParameter(query_url, 'orderWay', order_way);
            window.location.href = query_url;
        }
    });
    $(document).on('click', 'a.eam-apply-voucher', function (e) {
        e.preventDefault();
        var apply_voucher = $(this);
        var id_cart_rule = apply_voucher.attr('data-voucher-code');
        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: {
                eam_apply_voucher: true,
                id_cart_rule: id_cart_rule,
                token: eam_token
            }, beforeSend: function () {
                apply_voucher.prop('disabled', true);
            }, success: function (data) {
                if (typeof data !== "object") {
                    data = JSON.parse(data);
                }
                $('.eam-voucer-message').find('.alert').remove();
                var alert_class = data.success ? 'success' : 'danger';
                $('.eam-voucer-message').append('<div class="alert alert-' + alert_class + ' alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + data.message + '</div>')
            }, complete: function () {
                apply_voucher.prop('disabled', false);
            }
        })
    });
    if (typeof ets_am_product_view_link !== 'undefined' && ets_am_product_view_link) {
        setTimeout(function () {
            $.ajax({
                type: 'POST',
                url: ets_am_product_view_link,
                data: {
                    eam_get_access_key: true,
                    eam_id_seller: eam_id_seller,
                    token: eam_token
                }, success: function (data) {
                    if (typeof data !== 'object') {
                        data = JSON.parse(data);
                    }
                    if (data.success) {
                        var key = data.access_key;
                        setTimeout(function () {
                            checkAccessKey(key, eam_id_seller, ets_am_product_view_link);
                        }, 8000);
                    }
                }
            });
        }, 2000);
    }
    var loading = $('.stats-loading');
    $('.stats-box-info').on('click', '.box-info', function (e) {
        e.preventDefault();
        var box = $(this);
        $('.stats-box-info .box-inner').removeClass('active');
        box.find('.box-inner').addClass('active');
        var type = box.attr('data-type');
        var type_date_filter = $('select[name="type_date_filter"]').val();
        var date_from = $('input[name="date_from_reward"]').val();
        var date_to = $('input[name="date_to_reward"]').val();
        if (date_from && date_to) {
            getDataStats(type_date_filter, date_from, date_to, type, loading);
        }
    });

    $('.product-sales-filter').on('click', function (e) {
        e.preventDefault();
        var box = $('.stats-box-info .box-info.active');
        if (box.length) {
            var type = box.attr('data-type');
            var type_date_filter = $('select[name="type_date_filter"]').val();
            var date_from = $('input[name="date_from_reward"]').val();
            var date_to = $('input[name="date_to_reward"]').val();
            if (date_from && date_to) {
                getDataStats(type_date_filter, date_from, date_to, type, loading);
            }
        }
    });
    $('.product-sales-reset').on('click', function () {
        $('.box-info .box-inner').removeClass('active');
        $('.box-info.eam-turnover .box-inner').addClass('active');
        var type = 'TURNOVER';
        var type_date_filter = 'this_month';
        var date = new Date();
        var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
        var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        var date_from = (firstDay.getFullYear()) + '-' + (firstDay.getMonth() + 1) + '-' + firstDay.getDate();
        var date_to = (lastDay.getFullYear()) + '-' + (lastDay.getMonth() + 1) + '-' + lastDay.getDate();
        $('select[name="type_date_filter"]').val(type_date_filter);
        $('input[name="date_from_reward"]').val(date_from);
        $('input[name="date_to_reward"]').val(date_to);
        getDataStats(type_date_filter, date_from, date_to, type, loading);
    });

    $('.affiliate_product_filter').on('click', 'submit-filter', function (e) {
        e.preventDefault();
        var category = $('select[name="category"]').val();
    });

    $('.eam-box-tooltip').hover(function () {
        var box = $(this);
        var content = box.attr('data-title');
        var position = box.attr('data-placement');
        var tooltipContent = '<div class="box-tooltip ' + position + '">' + content + '<div class="arrow arrow-' + position + '"></div></div>';
        box.append(tooltipContent);
    },function(){
        $(this).find('.box-tooltip').remove();
    });
    $('.ets-am-register-program .term_required').change(function (event) {
        if ($(this).is(':checked')) {
            $('.ets-am-register-program button[name=submitEamRegisterProgram]').prop('disabled', false);
        } else {
            $('.ets-am-register-program button[name=submitEamRegisterProgram]').prop('disabled', true);
        }
    });

    $('.eam-ref-banner-code').click(function(event) {
        $(this).focus();
        $(this).select();
        document.execCommand("copy");
        $(this).prev('.eam-txt-tooltip').show();
        var $this = $(this);
        setTimeout(function(){
            $this.prev('.eam-txt-tooltip').hide();
        }, 2000);
    });
    if($('input[name=eam_code_ref]').length)
    {
        $('input[name=eam_code_ref]').closest('form').submit(function(event) {
            $(this).find('input[name=eam_code_ref]').next('.help-block').hide();
            var code_ref = $(this).find('input[name=eam_code_ref]').val();
            if(code_ref!=''){
                if(eamValidateEmail(code_ref) || (eamIsPositiveInteger(code_ref) && code_ref > 0)){
                    return true;
                }
                else{
                    $(this).find('input[name=eam_code_ref]').next('.help-block').css('color', 'red');
                    $(this).find('input[name=eam_code_ref]').next('.help-block').show();
                }
                return false;
            }

            return true;
        });
    }
    $('.eam-copy-clipboard').click(function(event) {
        if(eamCopyToClipboard($(this).attr('data-text'),null)){
            var old_html =  $(this).find('.eam-inner-copy-tooltip').html();
            var $this = $(this);
            $(this).find('.eam-inner-copy-tooltip').html($(this).find('.eam-inner-copy-tooltip').attr('data-copied'));
            setTimeout(function(){
                $this.find('.eam-inner-copy-tooltip').hide();
                $this.find('.eam-inner-copy-tooltip').html(old_html);
            }, 2500)
        }
    });
    $('.eam-copy-clipboard').hover(function() {
        $(this).find('.eam-inner-copy-tooltip').show();
    });
    $('.eam-copy-clipboard').mouseout(function() {
        $(this).find('.eam-inner-copy-tooltip').hide();
    });
});

$('.js-btn-submit-filter').closest('form').submit(function (event) {
    var filter_url = window.location.href;
    var form_data = $(this).serializeArray();
    $.each(form_data, function (index, el) {
        filter_url = eamUpdateQueryStringParameter(filter_url, el.name, el.value);
    });
    window.location.href = filter_url;
    return false;
});

function eamIsPositiveInteger(s) {
  return /^\+?[1-9][\d]*$/.test(s);
}

function checkAccessKey(key, id_seller, url) {
    $.ajax({
        type: 'POST',
        url: url,
        data: {
            eam_check_access_key: true,
            eam_access_key: key,
            eam_id_seller: id_seller,
            token: eam_token
        }, success: function () {
        }
    })
}

function getDataStats(type_date_filter, from, to, type, loading) {
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: {
            get_stat_reward: true,
            time_frame: type_date_filter,
            date_from: from,
            date_to: to,
            statistic: type,
            token: eam_token
        },
        beforeSend: function () {
            loading.show();
        }, success: function (data) {
            if (typeof data !== 'object') {
                data = JSON.parse(data);
            }
            var stats = data.stats;
            var countable = data.countable;
            var box = $('.box-info');
            box.each(function (index, element) {
                if ($(element).hasClass('eam-turnover')) {
                    $(element).find('.box-info-content').empty().text(countable.turnover)
                }
                if ($(element).hasClass('eam-rewards')) {
                    $(element).find('.box-info-content').empty().text(countable.total_earn)
                }
                if ($(element).hasClass('eam-orders')) {
                    $(element).find('.box-info-content').empty().text(countable.total_order)
                }
                if ($(element).hasClass('eam-views')) {
                    $(element).find('.box-info-content').empty().text(countable.view_count)
                }
                if ($(element).hasClass('eam-conversion-rate')) {
                    $(element).find('.box-info-content').empty().text(countable.conversion_rate)
                }
                if ($(element).hasClass('eam-net-profit')) {
                    $(element).find('.box-info-content').empty().text(countable.net_profit)
                }
            });
            var values = stats.data[0].values;
            var type = stats.type;
            var define_columns = [];
            $.each(values, function (k, v) {
                define_columns.push(v.x);
            });
            var axisLabel = stats.x_asis == 'date'? eam_chart_day : (stats.x_asis == 'month'? eam_chart_month : eam_chart_year);
            eamProductChart.xAxis.axisLabel(axisLabel).tickValues(define_columns).tickFormat(function (d) {
                if (stats.x_asis == 'date') {
                    return parseInt(d3.time.format('%d')(new Date(d)));
                } else if (stats.x_asis == 'month') {
                    return parseInt(d3.time.format('%m')(new Date(d)));
                } else if (stats.x_asis == 'year') {
                    return d3.time.format('%Y')(new Date(d))
                }
                return d;
            });
            eamProductChart.yAxis.tickFormat(function (d) {
                if (type == 'TURNOVER') {
                    return ets_am_currency_sign + ' ' + parseFloat(d).toFixed(1);
                } else if (type == 'ORDERS' || type == 'VIEWS') {
                    return (d == Math.floor(d)) ? d : null;
                } else if (type == 'CONVERSION_RATE') {
                    return parseFloat(d).toFixed(1) + '%';
                } else {
                    return parseFloat(d).toFixed(2);
                }
            });
            eamProductChartData.datum(stats.data)
                .call(eamProductChart);
            nv.utils.windowResize(eamProductChart.update);
        }, complete: function () {
            loading.hide();
        }
    })
}

function eamCopyToClipboard(text, el) {

    if (window.clipboardData && window.clipboardData.setData) {
        return clipboardData.setData("Text", text);
    } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
        if(el){
            $(el).select();
            return document.execCommand("copy");
        }
        var textarea = document.createElement("textarea");
        textarea.textContent = text;
        textarea.style.position = "fixed";
        document.body.appendChild(textarea);
        textarea.select();
        try {
            textarea.focus();
            return document.execCommand("copy");
        } catch (ex) {
            console.warn("Copy to clipboard failed.", ex);
            return false;
        } finally {
            document.body.removeChild(textarea);
        }
    }
}


function eamUpdateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    } else {
        return uri + separator + key + "=" + value;
    }
}

function eamCheckAmount(input) {
    formErrors = [];
    if (checkAmountXhr && checkAmountXhr.readyState !== 4) {
        checkAmountXhr.abort();
    }
    var eam_amount = input.val();
    if ($.trim(eam_amount) !== '') {
        if (!isNaN(eam_amount)) {
            checkAmountXhr = $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {
                    check_withdraw_amount: true,
                    amount: eam_amount,
                    token: eam_token
                },
                beforeSend: function () {
                    input.closest('.form-group').removeClass('has-error');
                    input.closest('.form-group').find('span.help-block').remove();
                },
                success: function (data) {
                    if (typeof data !== 'object') {
                        data = JSON.parse(data);
                    }
                    if (data.valid) {
                        var recevied = data.amount;
                        $('.eam-withdraw-boxes span.price').empty().append(recevied);
                    } else {
                        input.closest('.form-group').addClass('has-error');
                        input.closest('.form-group').find('span.help-block').remove();
                        var msg = data.msg;
                        formErrors.push(msg);
                        input.closest('.form-group').append('<span class="help-block">' + msg + '</span>');
                        $('.eam-withdraw-boxes span.price').empty().append(eam_currency_sign + '0.00');
                    }
                }
            })
        } else {
            $('.eam-withdraw-boxes span.price').empty().append(eam_currency_sign + '0.00');
        }
    } else {
        $('.eam-withdraw-boxes span.price').empty().append(eam_currency_sign + '0.00');
    }
}

function displayFormErrors(formInput, message) {
    formInput.closest('.form-group').addClass('has-error');
    formInput.closest('.form-group').find('span.help-block').remove().append('<span class="help-block">' + message + '</span>');
}

function affProductStats(data_ajax) {
    $.ajax({
        url: window.location.href,
        type: 'GET',
        data: data_ajax,
        beforeSend: function () {
            var loading = '<div class="stats-loading" style="display: block; padding: 0;">';
            loading += '<div class="loading-text"></div>';
            loading += '</div>';
            $('.js-btn-submit-filter').closest('#module-ets_affiliatemarketing-reward .card-block').prepend(loading);
        },
        success: function (res) {
            if (typeof res !== 'object') {
                res = JSON.parse(res);
            }
            var define_columns = [];
            $.each(res.data[0].values, function (index, el) {
                define_columns.push(el.x);
            });
            var axisLabel = res.x_asis == 'date'? eam_chart_day : (res.x_asis == 'month'? eam_chart_month : eam_chart_year);
            eamLineChartReward.xAxis
                .axisLabel(axisLabel)
                .rotateLabels(res.count_values > 31? -45 : 0)
                .tickValues(define_columns)
                .tickFormat(function (d) {
                if (res.x_asis == 'date') {
                    return parseInt(d3.time.format('%d')(new Date(d)));
                } else if (res.x_asis == 'month') {
                    return parseInt(d3.time.format('%m')(new Date(d)));
                } else if (res.x_asis == 'year') {
                    return d3.time.format('%Y')(new Date(d))
                }
                return d;
            });

            eamLineChartRewardData.datum(res.data)
                .call(eamLineChartReward);
            nv.utils.windowResize(eamLineChartReward.update);

        },
        complete: function () {
            $('.js-btn-submit-filter').closest('#module-ets_affiliatemarketing-reward .card-block').find('.stats-loading').remove();
        }
    });

}
function eamDisplayChartStat(res) {
    if (typeof res !== 'object') {
        res = JSON.parse(res);
    }
    var define_columns = [];
    $.each(res.data[0].values, function (index, el) {
        define_columns.push(el.x);
    });
    var axisLabel = res.x_asis == 'date'? eam_chart_day : (res.x_asis == 'month'? eam_chart_month : eam_chart_year);
    eamLineChartReward.xAxis
        .axisLabel(axisLabel)
        .rotateLabels(res.count_values > 31? -45 : 0)
        .tickValues(define_columns)
        .tickFormat(function (d) {
        if (res.x_asis == 'date') {
            return parseInt(d3.time.format('%d')(new Date(d)));
        } else if (res.x_asis == 'month') {
            return parseInt(d3.time.format('%m')(new Date(d)));
        } else if (res.x_asis == 'year') {
            return d3.time.format('%Y')(new Date(d))
        }
        return d;
    });

    eamLineChartRewardData.datum(res.data)
        .call(eamLineChartReward);
    nv.utils.windowResize(eamLineChartReward.update);
}
function eamAjaxChartStat(data_ajax) {
    $.ajax({
        url: window.location.href,
        type: 'GET',
        data: data_ajax,
        dataType:'json',
        beforeSend: function () {
            var loading = '<div class="stats-loading" style="display: block; padding: 0;">';
            loading += '<div class="loading-text"></div>';
            loading += '</div>';
            $('.js-btn-submit-filter').closest('#module-ets_affiliatemarketing-reward .card-block').prepend(loading);
        },
        success: function (res) {
            eamDisplayChartStat(res.stat_reward);
            eamDisplayPieChartReward(res.pie_reward)
        },
        complete: function () {
            $('.js-btn-submit-filter').closest('#module-ets_affiliatemarketing-reward .card-block').find('.stats-loading').remove();
        }
    });

}

function eamDisplayPieChartReward(res){
    if (typeof res !== 'object') {
        res = JSON.parse(res);
    }
    if(!res.length){
        d3.select("#eam_stats_reward_pie svg").selectAll('*').remove();
        $('#eam_stats_reward_pie .eam-chart-no-data').addClass('active');
    }
    else{
        $('#eam_stats_reward_pie .eam-chart-no-data').removeClass('active');
        nv.addGraph(function() {
          eamChartPieReward = nv.models.pieChart()
              .x(function(d) { return d.label })
              .y(function(d) { return d.value })
              .margin({left: 0, right: 0, top : 25, bottom : 0})
              .showLabels(true)
              .labelThreshold(.05)
              .pieLabelsOutside(false)
              .labelType("percent");
            eamChartPieReward.noData('No Data found');
            eamChartPieRewardData = d3.select("#eam_stats_reward_pie svg")
                .datum(res)
                .transition().duration(350)
                .call(eamChartPieReward);
            nv.utils.windowResize(eamChartPieReward.update);
            eamChartPieReward.tooltipContent(function(key, y, e, graph) {
                return '<h3>' + key + '</h3>' +
                       '<p>' +  y + ' '+eam_chart_currency_code+ '</p>';
            });
            eamChartPieReward.update();
          return eamChartPieReward;
        });
    }
}

function eamGetUrlVars() {
    vars = {};
    parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
        vars[key] = value;
    });
    return vars;
}

function eamShowMessage(message, type) {

    notify_html = '<div class="eam-notify ' + type + '" style="display: none;">';
    notify_html += message;
    notify_html += '</div>';

    if ($('body .eam-notify').length == 0) {
        $('body').append(notify_html);
        $('body .eam-notify').fadeIn(500);
        setTimeout(function () {
            $('body .eam-notify').fadeOut(1000, function () {
                $('body .eam-notify').remove();
            });

        }, 4000)

    }
}

function eamShowAlert(message, type) {
    var div_alert = '<div class="eam-alert alert alert-' + type + '">';
    div_alert += '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    div_alert += message;
    div_alert += '</div>';
    $('.eam-alert-box').html(div_alert);
}

function eamValidateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}


function eamLoadStats() {
    if ($('#eam_data_filter_stat_reward').length == 0) {
        return;
    }
    formData = $('#eam_data_filter_stat_reward').serializeArray();
    data_ajax = {
        getStatDataReward: true,
        token: eam_token
    };
    data_ajax.filter_status = [];
    $.each(formData, function (index, el) {
        if (el.name == 'date_from') {
            data_ajax.filter_date_from = el.value;
        }
        if (el.name == 'date_to') {
            data_ajax.filter_date_to = el.value;
        }
        if (el.name == 'status[]') {
            data_ajax.filter_status.push(el.value);
        }
    });

    $.ajax({
        url: window.location.href,
        type: 'GET',
        data: data_ajax,
        beforeSend: function () {
            $('.eam-statistic-reward .stats-loading').show();
        },
        success: function (res) {

            if (typeof res !== 'object') {
                res = JSON.parse(res);
            }

            if (typeof eamLineChart == 'undefined') {
                eamLineChart = new Chart(eamLineChartCtx, {
                    type: 'line',
                    data: {
                        labels: res.labels,
                        datasets: res.datasets
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: res.y_label
                                }
                            }],
                            xAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: res.x_label
                                }
                            }],
                        }
                    }
                });
            } else {
                eamLineChart.data.labels = res.labels;
                eamLineChart.data.datasets = res.datasets;
                eamLineChart.update();
            }
        },
        complete: function () {
            $('.eam-statistic-reward .stats-loading').hide();
        }
    })
}

function eamSetDateFilterStat(input_dateranger, separate_date, separate_ranger) {
    if ($(input_dateranger).length == 0) {
        return;
    }
    dateranger = $(input_dateranger).val();
    if(dateranger){
        dateRangerArray = dateranger.split(' ' + separate_ranger + ' ');
        from = dateRangerArray[0];
        to = dateRangerArray[1];

        $(input_dateranger).parent().find('input[name=date_from]').val(from.replace(new RegExp(separate_date, "g"), '-'));
        $(input_dateranger).parent().find('input[name=date_to]').val(to.replace(new RegExp(separate_date, "g"), '-'));
    }

}

function eamIsFloat(n) {
    return !isNaN(n) || (!isNaN(n) && n.toString().indexOf('.') != -1);
}
function eamIsEmail(s)
{
	var reg = unicode_hack(/^[a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z\p{L}0-9]+$/i, false);
	return reg.test(s);
}