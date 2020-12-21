import ConstBase from 'Consts/base.js';

class CommonStatusConst extends ConstBase {
  constructor() {
    super([
      ['DISABLE', 0, 'common.disable'],
      ['ENABLE', 1, 'common.enable'],
    ]);
  }
}

const CommonStatus = new CommonStatusConst();
export { CommonStatus as default };
