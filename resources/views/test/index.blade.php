@extends('layouts.bootstrap')

@section('title')サンプル@endsection

@section('js')
<script>
$(function () {
	// クリックで画像を選択する場合
	$('#drop_area').on('click', function () {
		$('#input_file').click();
	});

	$('#input_file').on('change', function () {
	// 画像が複数選択されていた場合
	if (this.files.length > 1) {
		alert('アップロードできる画像は1つだけです');
		$('#input_file').val('');
		return;
	}
	handleFiles(this.files);
	});
	// ドラッグしている要素がドロップ領域に入ったとき・領域にある間
	$('#drop_area').on('dragenter dragover', function (event) {
		event.stopPropagation();
		event.preventDefault();
		$('#drop_area').css('border', '1px solid #333');  // 枠を実線にする
	});
	// ドラッグしている要素がドロップ領域から外れたとき
	$('#drop_area').on('dragleave', function (event) {
		event.stopPropagation();
		event.preventDefault();
		$('#drop_area').css('border', '1px dashed #aaa');  // 枠を点線に戻す
	});
	// ドラッグしている要素がドロップされたとき
	$('#drop_area').on('drop', function (event) {
		event.preventDefault();

		$('#input_file')[0].files = event.originalEvent.dataTransfer.files;

		// 画像が複数選択されていた場合
		if ($('#input_file')[0].files.length > 1) {
			alert('アップロードできる画像は1つだけです');
			$('#input_file').val('');
			return;
		}
		handleFiles($('#input_file')[0].files);
	});
	
	// 選択された画像ファイルの操作
	function handleFiles(files) {
		var file = files[0];
		var imageType = 'image.*';

		// ファイルが画像が確認する
		if (! file.type.match(imageType)) {
			/*
			alert('画像を選択してください');
			$('#input_file').val('');
			$('#drop_area').css('border', '1px dashed #aaa');
			 */
			return;
		}

		$('#drop_area').hide();  // いちばん上のdrop_areaを非表示にします
		$('#icon_clear_button').show();  // icon_clear_buttonを表示させます

		var img = document.createElement('img');  // <img>をつくります
		var reader = new FileReader();
		reader.onload = function () {  // 読み込みが完了したら
			img.src = reader.result;  // readAsDataURLの読み込み結果がresult
			$('#preview_field').append(img);  // preview_filedに画像を表示
		}
		reader.readAsDataURL(file); // ファイル読み込みを非同期でバックグラウンドで開始
	} 
	// アイコン画像を消去するボタン
	$('#icon_clear_button').on('click', function () {
		$('#preview_field').empty();  // 表示していた画像を消去
		$('#input_file').val('');  // inputの中身を消去
		$('#drop_area').show();  // drop_areaをいちばん前面に表示
		$('#icon_clear_button').hide();  // icon_clear_buttonを非表示
		$('#drop_area').css('border', '1px dashed #aaa');  // 枠を点線に変更
	});
});
</script>
@endsection

@section('content')
<style type="text/css">
.user-icon-dnd-wrapper {
  position: relative;
  width: 100px;
  height: 100px;
}

#preview_field {
  position: absolute;
  top: 0;
  left: 0;
  width: 100px;
  height: 100px;
}

#drop_area {
  position: absolute;
  top: 0;
  left: 0;
  width: 100px;
  height: 100px;
  border: 1px dashed #aaa;
  cursor: pointer;
}

#icon_clear_button {
  display: none;
  position: absolute;
  top: -4px;
  right: -8px;
  width: 24px;
  height: 24px;
  border: 1px solid #777;
  border-radius: 50%;
  cursor: pointer;
}

#input_file {
  width: 100px;
  height: 100px;
  opacity: 0;
}

#input_file:focus {
  opacity: 1;
}
</style>
<div id="container" class="container">
<label for="icon" class="label-name">icon</label>
<div class="user-icon-dnd-wrapper">
  <input type="file" name="icon" id="input_file" accept="image/*">
  <div id="preview_field"></div>
  <div id="drop_area">drag and drop<br>or<br>click here.</div>
  <div id="icon_clear_button">X</div>
</div>
</div>
@endsection
