// TRANSLITERATION RULES
// Input path: genconvert/input/my-t-my-s0-zawgyi.txt
function getAllRulesZ2U() {
  var rules0 = [
    {
      p: RegExp('^' + '([\u1000-\u1021])\u103A\u1064'),
      s: '\u1004\u103A\u1039$1\u103B',
    },
    {
      p: RegExp('^' + '([\u1000-\u1021])\u1064'),
      s: '\u1004\u103A\u1039$1',
    },
    {
      p: RegExp('^' + '\u1064'),
      s: '\u1004\u103A\u1039',
    },
    {
      p: RegExp('^' + '([\u1000-\u1021])\u108B'),
      s: '\u1004\u103A\u1039$1\u102D',
    },
    {
      p: RegExp('^' + '([\u1000-\u1021])\u108C'),
      s: '\u1004\u103A\u1039$1\u102E',
    },
    {
      p: RegExp('^' + '([\u1000-\u1021])\u108D'),
      s: '\u1004\u103A\u1039$1\u1036',
    },
    {
      p: RegExp('^' + '([\u1000-\u1021])\u103A\u1033\u108B'),
      s: '\u1004\u103A\u1039$1\u103B\u102D\u102F',
    },
    {
      p: RegExp('^' + '([\u1000-\u1021])\u103A\u108B'),
      s: '\u1004\u103A\u1039$1\u103B\u102D',
    },
    {
      p: RegExp('^' + '([\u1000-\u1021])\u103A\u108C'),
      s: '\u1004\u103A\u1039$1\u103B\u102E',
    },
    {
      p: RegExp('^' + '([\u1000-\u1021])\u103A\u108D'),
      s: '\u1004\u103A\u1039$1\u103B\u1036',
    },
    {
      p: RegExp('^' + '([\u1000-\u1021])\u103A\u108E'),
      s: '$1\u103B\u102D\u1036',
    },
    {
      p: RegExp('^' + '\u108B'),
      s: '\u1004\u103A\u1039\u102D',
    },
    {
      p: RegExp('^' + '\u108C'),
      s: '\u1004\u103A\u1039\u102E',
    },
    {
      p: RegExp('^' + '\u108D'),
      s: '\u1004\u103A\u1039\u1036',
    },
    {
      p: RegExp('^' + '\u106A'),
      s: '\u1009',
    },
    {
      p: RegExp('^' + '\u106B'),
      s: '\u100A',
    },
    {
      p: RegExp('^' + '\u108F'),
      s: '\u1014',
    },
    {
      p: RegExp('^' + '\u1090'),
      s: '\u101B',
    },
    {
      p: RegExp('^' + '\u1086'),
      s: '\u103F',
    },
    {
      p: RegExp('^' + '[\u103A\u107D]'),
      s: '\u103B',
    },
    {
      p: RegExp('^' + '([\u103B\u107E-\u1084])+'),
      s: '\u103C',
    },
    {
      p: RegExp('^' + '\u103C*\u108A'),
      s: '\u103D\u103E',
    },
    {
      p: RegExp('^' + '\u103C'),
      s: '\u103D',
    },
    {
      p: RegExp('^' + '[\u103D\u1087]'),
      s: '\u103E',
    },
    {
      p: RegExp('^' + '\u1088'),
      s: '\u103E\u102F',
    },
    {
      p: RegExp('^' + '\u1089'),
      s: '\u103E\u1030',
    },
    {
      p: RegExp('^' + '\u1033'),
      s: '\u102F',
    },
    {
      p: RegExp('^' + '\u1034'),
      s: '\u1030',
    },
    {
      p: RegExp('^' + '\u1039'),
      s: '\u103A',
    },
    {
      p: RegExp('^' + '[\u1094\u1095]'),
      s: '\u1037',
    },
    {
      p: RegExp('^' + '\u1025\u1039'),
      s: '\u1009\u103A',
    },
    {
      p: RegExp('^' + '\u1025\u1061'),
      s: '\u1009\u1039\u1001',
    },
    {
      p: RegExp('^' + '\u1025\u1062'),
      s: '\u1009\u1039\u1002',
    },
    {
      p: RegExp('^' + '\u1025\u1065'),
      s: '\u1009\u1039\u1005',
    },
    {
      p: RegExp('^' + '\u1025\u1068'),
      s: '\u1009\u1039\u1007',
    },
    {
      p: RegExp('^' + '\u1025\u1076'),
      s: '\u1009\u1039\u1013',
    },
    {
      p: RegExp('^' + '\u1025\u1078'),
      s: '\u1009\u1039\u1015',
    },
    {
      p: RegExp('^' + '\u1025\u107A'),
      s: '\u1009\u1039\u1017',
    },
    {
      p: RegExp('^' + '\u1025\u1079'),
      s: '\u1009\u1039\u1016',
    },
    {
      p: RegExp('^' + '\u105A'),
      s: '\u102B\u103A',
    },
    {
      p: RegExp('^' + '\u1060'),
      s: '\u1039\u1000',
    },
    {
      p: RegExp('^' + '\u1061'),
      s: '\u1039\u1001',
    },
    {
      p: RegExp('^' + '\u1062'),
      s: '\u1039\u1002',
    },
    {
      p: RegExp('^' + '\u1063'),
      s: '\u1039\u1003',
    },
    {
      p: RegExp('^' + '\u1065'),
      s: '\u1039\u1005',
    },
    {
      p: RegExp('^' + '[\u1066\u1067]'),
      s: '\u1039\u1006',
    },
    {
      p: RegExp('^' + '\u1068'),
      s: '\u1039\u1007',
    },
    {
      p: RegExp('^' + '\u1069'),
      s: '\u1039\u1008',
    },
    {
      p: RegExp('^' + '\u106C'),
      s: '\u1039\u100B',
    },
    {
      p: RegExp('^' + '\u106D'),
      s: '\u1039\u100C',
    },
    {
      p: RegExp('^' + '\u1070'),
      s: '\u1039\u100F',
    },
    {
      p: RegExp('^' + '[\u1071\u1072]'),
      s: '\u1039\u1010',
    },
    {
      p: RegExp('^' + '\u1096'),
      s: '\u1039\u1010\u103D',
    },
    {
      p: RegExp('^' + '[\u1073\u1074]'),
      s: '\u1039\u1011',
    },
    {
      p: RegExp('^' + '\u1075'),
      s: '\u1039\u1012',
    },
    {
      p: RegExp('^' + '\u1076'),
      s: '\u1039\u1013',
    },
    {
      p: RegExp('^' + '\u1077'),
      s: '\u1039\u1014',
    },
    {
      p: RegExp('^' + '\u1078'),
      s: '\u1039\u1015',
    },
    {
      p: RegExp('^' + '\u1079'),
      s: '\u1039\u1016',
    },
    {
      p: RegExp('^' + '\u107A'),
      s: '\u1039\u1017',
    },
    {
      p: RegExp('^' + '[\u107B\u1093]'),
      s: '\u1039\u1018',
    },
    {
      p: RegExp('^' + '\u107C'),
      s: '\u1039\u1019',
    },
    {
      p: RegExp('^' + '\u1085'),
      s: '\u1039\u101C',
    },
    {
      p: RegExp('^' + '\u108E'),
      s: '\u102D\u1036',
    },
    {
      p: RegExp('^' + '\u106E'),
      s: '\u100D\u1039\u100D',
    },
    {
      p: RegExp('^' + '\u106F'),
      s: '\u100D\u1039\u100E',
    },
    {
      p: RegExp('^' + '\u1091'),
      s: '\u100F\u1039\u100D',
    },
    {
      p: RegExp('^' + '\u1092'),
      s: '\u100B\u1039\u100C',
    },
    {
      p: RegExp('^' + '\u1097'),
      s: '\u100B\u1039\u100B',
    },
    {
      p: RegExp('^' + '\u104E'),
      s: '\u104E\u1004\u103A\u1038',
    },
  ];
  var rules1 = [
    {
      p: RegExp('^' + '\u1040([^\u1040-\u1049])'),
      s: '\u101D$1',
      matchOnStart: 'true',
    },
    {
      p: RegExp('^' + '\u1044([^\u1040-\u1049])'),
      s: '\u104E$1',
      matchOnStart: 'true',
      revisit: 0,
    },
    {
      p: RegExp('^' + '([^\u1040-\u1049])\u1040$'),
      s: '$1\u101D',
    },
    {
      p: RegExp('^' + '([^\u1040-\u1049])\u1044$'),
      s: '$1\u104E',
    },
    {
      p: RegExp('^' + '([\u102B-\u103F])\u1040([^\u1040-\u1049])'),
      s: '$1\u101D$2',
    },
    {
      p: RegExp('^' + '([\u102B-\u103F])\u1044([^\u1040-\u1049])'),
      s: '$1\u104E$2',
    },
  ];
  var rules2 = [
    {
      p: RegExp('^' + '([ \u00A0\u1680\u2000-\u200D\u202F\u205F\u2060\u3000\uFEFF])\u1037'),
      s: '\u1037$1',
    },
    {
      p: RegExp('^' + '([ \u00A0\u1680\u2000-\u200D\u202F\u205F\u2060\u3000\uFEFF]+)([\u102B-\u1030\u1032-\u103B\u103D\u103E])'),
      s: '$2',
    },
    {
      p: RegExp('^' + '\u1037+'),
      s: '\u1037',
    },
    {
      p: RegExp('^' + '\u1031+\u1004\u103A\u1039([\u1000-\u1021])'),
      s: '\u1004\u103A\u1039$1\u1031',
    },
    {
      p: RegExp('^' + '\u1031+\u1037+([\u1000-\u1021])'),
      s: '$1\u1031\u1037',
    },
    {
      p: RegExp('^' + '\u1031+\u103C([\u1000-\u1021])'),
      s: '$1\u103C\u1031',
    },
    {
      p: RegExp('^' + '\u1031+([\u1000-\u1021])([\u103B\u103D\u103E]+)'),
      s: '$1$2\u1031',
    },
    {
      p: RegExp('^' + '\u1031+([\u1000-\u102A])'),
      s: '$1\u1031',
    },
  ];
  var rules3 = [
    {
      p: RegExp('^' + '\u103B\u103A'),
      s: '\u103A\u103B',
    },
    {
      p: RegExp('^' + '\u1025\u102E'),
      s: '\u1026',
    },
    {
      p: RegExp('^' + '\u103A\u1037'),
      s: '\u1037\u103A',
    },
    {
      p: RegExp('^' + '\u1036([\u103B-\u103E]*)([\u102B-\u1030\u1032]+)'),
      s: '$1$2\u1036',
    },
    {
      p: RegExp('^' + '([\u102B\u102C\u102F\u1030])([\u102D\u102E\u1032])'),
      s: '$2$1',
    },
    {
      p: RegExp('^' + '\u103C([\u1000-\u1021])'),
      s: '$1\u103C',
    },
  ];
  var rules4 = [
    {
      p: RegExp('^' + '([\u103B-\u103E])\u1039([\u1000-\u1021])'),
      s: '\u1039$2$1',
    },
    {
      p: RegExp('^' + '\u103C\u103A\u1039([\u1000-\u1021])'),
      s: '\u103A\u1039$1\u103C',
    },
    {
      p: RegExp('^' + '\u1036([\u103B-\u103E]+)'),
      s: '$1\u1036',
    },
  ];
  var rules5 = [
    {
      p: RegExp('^' + '([\u103C-\u103E]+)\u103B'),
      s: '\u103B$1',
    },
    {
      p: RegExp('^' + '([\u103D\u103E]+)\u103C'),
      s: '\u103C$1',
    },
    {
      p: RegExp('^' + '\u103E\u103D'),
      s: '\u103D\u103E',
    },
    {
      p: RegExp('^' + '([\u1031]+)([\u102B-\u1030\u1032]*)\u1039([\u1000-\u1021])'),
      s: '\u1039$3$1$2',
    },
    {
      p: RegExp('^' + '([\u102B-\u1030\u1032]+)\u1039([\u1000-\u1021])'),
      s: '\u1039$2$1',
    },
    {
      p: RegExp('^' + '([\u103B-\u103E]*)([\u1031]+)([\u103B-\u103E]*)'),
      s: '$1$3$2',
    },
    {
      p: RegExp('^' + '\u1037([\u102D-\u1030\u1032\u1036\u103B-\u103E]+)'),
      s: '$1\u1037',
    },
    {
      p: RegExp('^' + '([\u102B-\u1030\u1032]+)([\u103B-\u103E]+)'),
      s: '$2$1',
    },
    {
      p: RegExp('^' + '([\u1000-\u1021])([\u102B-\u1032\u1036\u103B-\u103E])\u103A([\u1000-\u1021])'),
      s: '$1\u103A$2$3',
    },
  ];
  var rules6 = [
    {
      p: RegExp('^' + '\u1005\u103B'),
      s: '\u1008',
    },
    {
      p: RegExp('^' + '([\u102B-\u1032])([\u103B-\u103E])'),
      s: '$2$1',
    },
    {
      p: RegExp('^' + '([\u103C-\u103E])\u103B'),
      s: '\u103B$1',
    },
    {
      p: RegExp('^' + '([\u103D\u103E])\u103C'),
      s: '\u103C$1',
    },
    {
      p: RegExp('^' + '\u103E\u103D'),
      s: '\u103D\u103E',
    },
    {
      p: RegExp('^' + '\u1038([\u000136u\u102B-\u1030\u1032\u1037\u103A-\u103F])'),
      s: '$1\u1038',
    },
    {
      p: RegExp('^' + '\u1036\u102F'),
      s: '\u102F\u1036',
    },
  ];
  var rules7 = [
    {
      p: RegExp('^' + '\u102D\u102D+'),
      s: '\u102D',
    },
    {
      p: RegExp('^' + '\u102E\u102E+'),
      s: '\u102E',
    },
    {
      p: RegExp('^' + '\u102F\u102F+'),
      s: '\u102F',
    },
    {
      p: RegExp('^' + '\u1030\u1030+'),
      s: '\u1030',
    },
    {
      p: RegExp('^' + '\u1032\u1032+'),
      s: '\u1032',
    },
    {
      p: RegExp('^' + '\u1036\u1036+'),
      s: '\u1036',
    },
    {
      p: RegExp('^' + '\u1037\u1037+'),
      s: '\u1037',
    },
    {
      p: RegExp('^' + '\u1039\u1039+'),
      s: '\u1039',
    },
    {
      p: RegExp('^' + '\u103A\u103A+'),
      s: '\u103A',
    },
    {
      p: RegExp('^' + '\u103B\u103B+'),
      s: '\u103B',
    },
    {
      p: RegExp('^' + '\u103C\u103C+'),
      s: '\u103C',
    },
    {
      p: RegExp('^' + '\u103D\u103D+'),
      s: '\u103D',
    },
    {
      p: RegExp('^' + '\u103E\u103E+'),
      s: '\u103E',
    },
    {
      p: RegExp('^' + '\u102F[\u1030\u103A]'),
      s: '\u102F',
    },
    {
      p: RegExp('^' + '\u102D\u102E'),
      s: '\u102E',
    },
    {
      p: RegExp('^' + '([ \u00A0\u1680\u2000-\u200D\u202F\u205F\u2060\u3000\uFEFF])+([\u102B-\u1032\u1036-\u103E])'),
      s: '$2',
    },
    {
      p: RegExp('^' + '\u200B+'),
      s: '',
      matchOnStart: 'true',
    },
    {
      p: RegExp('^' + '\u200B+$'),
      s: '',
    },
    {
      p: RegExp('^' + '[ \u00A0\u1680\u2000-\u200D\u202F\u205F\u2060\u3000\uFEFF]*\u200B[ \u00A0\u1680\u2000-\u200D\u202F\u205F\u2060\u3000\uFEFF]*'),
      s: '\u200B',
    },
  ];
  return [rules0, rules1, rules2, rules3, rules4, rules5, rules6, rules7];
}
// END OF TRANSLITERATION RULES
