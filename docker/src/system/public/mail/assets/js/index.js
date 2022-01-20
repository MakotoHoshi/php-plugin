/**
 * 
 * ラジオボタン/チェックボックスの選択でテキストフィールドのdisabledを切り替える
 * 
 * @param {string} trigger トリガーとなるラジオボタン/チェックボックス
 * @param {string} target 切り替え対象のテキストフィールド
 * @param {string} all トリガーを含む全てのラジオボタン/チェックボックス
 */
function switchField(params) {
    if($(params.trigger + ':checked').length == 1){
        if($(params.target).is(':disabled')){
            $(params.target).prop('disabled', false);
        }
    }else{
        $(params.target).val('');
    }
    $(params.all).change(function(){
        if($(params.trigger + ':checked').length == 1){
            if($(params.target).is(':disabled')){
                $(params.target).prop('disabled', false);
            }
        }else{
            $(params.target).val('');
            $(params.target).prop('disabled', true);
        }
    });
}