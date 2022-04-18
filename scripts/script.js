$(document).ready(function() {
  $('#sign-up').click(() => {
    $('.content').addClass('right-panel-active');
  });

  $('#sign-in').click(() => {
    $('.content').removeClass('right-panel-active');
  });

  modalClick();
  settingsFormSubmit();
  addFriendHandler();
  deleteFriendHandler();

  $('#settings-button').click(() => {
    $.ajax({
      type: 'GET',
      url: './settings.html',
      dataType: 'html',
      success: function(data) {
        var html = document.createElement('html');
        html.innerHTML = data;
        modal = html.querySelector('.modal');
        // append the modal settings box
        $('body').append(modal);

        modalClick();
        settingsFormSubmit();
      },
      error: function(msg) {
        // there was a problem
        alert('There was a problem: ' + msg.status + ' ' + msg.statusText);
      }
    });
  });

  $('#add-game-button').click(() => {
    if($('#add-game-container').css('display') == 'none') {
      $('#games ul').css('display', 'none');
      $('#add-game-container').css('display', 'flex');
      $('#add-game-button').css('transform', 'rotate(45deg)');
    } else {
      $('#games ul').css('display', 'block');
      $('#add-game-container').css('display', 'none');
      $('#add-game-button').css('transform', 'rotate(0deg)');
    }
  });

  $('.friends button, #hp-fr').click(function() {
    window.location = './friends.php';
  });

  $('.make-post .un-clicked').focus(() => {
    $('.make-post .un-clicked').css('display', 'none');
    $('.make-post .clicked').css('display', 'block');
    $('#make-post-text').focus();
  });

  $('#make-post-form').on("click", "#close-make-post", function() {
    $('.make-post .un-clicked').css('display', 'block');
    $('.make-post .clicked').css('display', 'none');
    $("#make-post-text").val("");
    $("#make-post-title").val("");
    $("#categoryinp").val("0");
    $("#upload-photo").val("");
    $("#remove-file").remove();
    $('.new-file-name').remove();
    $('.post-error').remove();
  });

  $(".posts").on('click', '.post-image', function(e) {
    var img = $(this)
    var post = img.closest('.post');
    var open_img = post.find('.post-image-open');

    if(open_img.length) {
      open_img.remove();
      img.attr('title', 'Open image');
    } else {
      post.append(img.clone().addClass('post-image-open'));
      img.attr('title', 'Close image');
    }
  });

  $('#home-btn').click(() => {
    window.location = './index.php';
  });

  $('#profile-btn').click(() => {
    window.location = './profile.php';
  });

  $('#friends-btn').click(() => {
    window.location = './friends.php';
  });

  $('#friends button').click(() => {
    window.location = './friends.php';
  });

  $('#friends-search-form').submit(function(e) {
    e.preventDefault();

    form = $(this);
    var url = form.attr('action');
    $('.error').remove();
    $('.msg').remove();

    var formData = form.serialize() + '&' + form.find('button').attr('name') + '=';

    $.ajax({
      type: 'POST',
      url: url,
      data: formData,
      dataType: 'JSON',
      success: function(data) {
        if(data.errors) {
          var out = '<div class="error">' + data.errors + '</div>';
          form.closest('#search-wrapper').prepend(out);
        } else {
          $('#friends-search-res').empty();

          $('#friends-search-collapse').show();

          var out = "";
          data.forEach(function(user) {
            out += "<div class='profile-small'>";
            out += "<div class='hidden-id'>" + user.user_id + "</div>";
            out += "<img class='profile-pic-small' alt='Profile Picture' src='./" + user.path + "'>";
            out += "<div class='profile-wrapper'>";
            out += "<div class='user-name'><a href='./profile.php?user_id=" + user.user_id + "'>";
            out += user.fname + " " + user.lname + "</a></div>";
            out += "<p class='bio-small'>" + user.bio + "</p></div>";
            out += "<button title='Add " + user.fname + " " + user.lname + "as a friend' class='add-friend-btn circle plus add-img'></button>";
            out += "</div>";
          });

          $('#friends-search-res').prepend(out);
          addFriendHandler();
        }
      },
      error: function(msg) {
        // there was a problem
        alert('There was a problem: ' + msg.status + ' ' + msg.statusText);
      }
    });
  });

  $(".collapse-header").click(function() {
    $header = $(this);

    $content = $header.next();
    //open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
    $content.slideToggle(500, function() {
      var out = "";
      if($content.is(":visible")) {
        out += "<i class='fa fa-fw fa-chevron-down'></i>" + $header.attr('data-content');

      } else {
        out += "<i class='fa fa-fw fa-chevron-right'></i>" + $header.attr('data-content');
      }
      $header.empty();
      $header.prepend(out);
    });
  });

  $('.post-filter').change(function() {
    $("#filter-submit").trigger("click");
  });

  $('.upload-photo').click(() => {
    $("#upload-photo").trigger("click");
  });

  $('#upload-photo').change(function() {
    var file = "<h4 class='new-file-name'>" + $(this).val().replace(/C:\\fakepath\\/i, '') + "</h4>";
    var btn = "<button type='button' id='remove-file' name='remove-file'>Remove file</button>";
    $(btn).insertAfter(this);
    $(file).insertAfter(this);
  });

  $('#make-post-form').on("click", "#remove-file", function() {
    $("#upload-photo").val("");
    $('#remove-file').remove();
    $(".new-file-name").remove();
  });

  $("#filter-form").submit(function(e) {
    e.preventDefault();

    form = $(this);
    var url = form.attr('action');
    var filter = $('#filter').val();
    var formData = new FormData();
    formData.append('filter', filter);
    formData.append(form.find('button').attr('name'), '');
    let ret_rem_post = [false, null];
    $(".post").remove();
    getPosts(url, formData, ret_rem_post, 1);

  });

  $("#make-post-form").submit(function(e) {
    e.preventDefault();

    form = $(this);
    var url = form.attr('action');
    $('.post-error').remove();
    $('.msg').remove();
    $('#close-make-post').remove();

    let ret_rem_post = [false, null];
    if($('#make-post-form #remove-file').length) {
      $('#remove-file').remove();
      ret_rem_post[1] = $('.new-file-name').text();
      $(".new-file-name").remove();
      ret_rem_post[0] = true;
    }

    var img = $('#upload-photo').prop('files')[0];

    var formData = new FormData();
    formData.append('upload-photo', img);
    formData.append('text', $('#make-post-text').val());
    formData.append('categoryinp', $('#categoryinp').val());
    formData.append('gameinp', $('#gameinp').val());
    formData.append($('#post').attr('name'), '');
    getPosts(url, formData, ret_rem_post, 0);
  });

  $('#follow, #unfollow').click(function() {
    var btn = $(this);
    var formData = new FormData();
    formData.append(btn.attr('id'), '');
    formData.append('gameid', btn.attr('data-gameid'));

    $.ajax({
      type: 'POST',
      url: './games_db.php',
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      dataType: 'JSON',
      success: function(data) {
        if(data.errors) {
          var out = '<div class="error">' + data.errors + '</div>';
          btn.insertBefore(out);
        } else {
          if(btn.attr('id') == 'follow') {
            btn.text('Unfollow');
            btn.attr('id', 'unfollow');
          } else {
            btn.text('Follow');
            btn.attr('id', 'follow');
          }
        }
      },
      error: function(msg) {
        // there was a problem
        alert('There was a problem: ' + msg.status + ' ' + msg.statusText);
      }
    });
  });
});

function getPosts(url, formData, ret_rem_post, mode) {
  $.ajax({
    type: 'POST',
    url: url,
    cache: false,
    contentType: false,
    processData: false,
    data: formData,
    dataType: 'JSON',
    success: function(data) {
      if(data.errors) {
        var out = "<p class='post-error'>" + data.errors + "</p>";
        $("<button type='button' id='close-make-post'>Close</button>").insertBefore("#post");
        if(ret_rem_post[0] == true) {
          var file = "<h4 class='new-file-name'>" + ret_rem_post[1] + "</h4>";
          var btn = "<button type='button' id='remove-file' name='remove-file'>Remove file</button>";
          $(btn).insertAfter('#upload-photo');
          $(file).insertAfter('#upload-photo');
        }
        if(mode == 0) {
          $(out).appendTo("#make-post-form");
        } else if(mode == 1) {
          $(out).insertAfter("#posts-header");
        }
      } else {
        var posts = $('.posts');

        var out = "";
        for(post in data) {
          post = data[post];
          out += "<div class='post'><div class='post-small-content'>";
          if(post.postimgpath) {
            out += "<img class='post-image' src='./" + post.postimgpath + "' alt='Post image' title='Open image' />";
          }

          out += "<img class='user-image' src='./" + post.pfimgpath + "' alt='User picture' />";
          out += "<a href='./profile.php?user_id=" + post.user_id + "'><h3>" + post.fname + " " + post.lname + "</h3></a>";
          out += "<h5> • " + post.title + " • </h5>";

          if(post.game_id) {
            out += "<a href='./games.php?game_id=" + post.game_id + "'><h3>" + post.game_name + "</h3></a>";
          }

          out += "<p>" + post.text + "</p><h5 class='post-date'>" + post.date + "</h5></div></div>";
        }

        $('.post').remove();
        $(out).insertAfter("#posts-header");

        if(mode == 0) {
          $("<button id='close-make-post'>Close</button>").insertBefore("#post");
        }
        $("#make-post-text").val("");
        $("#make-post-title").val("");
        $('.make-post .un-clicked').css('display', 'block');
        $('.make-post .clicked').css('display', 'none');
        $('.post-error').remove();

      }
    },
    error: function(msg) {
      // there was a problem
      alert('There was a problem: ' + msg.status + ' ' + msg.statusText);
    }
  });
}

function modalClick() {
  $('.modal').click(function(e) {
    if(e.target == this) {
      $(this).remove();
    }
  });
}

function getProfile(url, form) {
  var formData = form.serialize() + '&' + form.find('button').attr('name') + '=';
  $.ajax({
    type: 'POST',
    url: url,
    data: formData,
    dataType: 'JSON',
    success: function(data) {
      if(data.errors) {
        var out = '<div class="error">' + data.errors + '</div>';
        form.parent().find('.setting-header').after(out);
      } else {
        $(".profile img").remove();
        $(".profile h1").remove();
        $(".profile p").remove();
        console.log(data[0]);
        var out = "";
        out += data[2].replace('\\', "");
        out += "<h1>" + data[0] + "</h1>";
        if(data[1] == null) {
          out += "<p> </p>";
        } else {
          out += "<p>" + data[1] + "</p>";
        }

        $('.profile').prepend(out);
      }
    },
    error: function(msg) {
      // there was a problem
      alert('There was a problem: ' + msg.status + ' ' + msg.statusText);
    }
  });
}

function addFriendHandler() {
  $('.add-friend-btn').click(function(e) {
    $('.error').remove();
    $('.msg').remove();

    var btn = $(this);
    var profile = $(this).parent();
    var formData = new FormData();
    formData.append('add-friend-btn', '');
    formData.append('friend-id', btn.parent().find('.hidden-id').text());

    $.ajax({
      type: 'POST',
      url: './friends_db.php',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      dataType: 'JSON',
      success: function(data) {
        if(data.errors) {
          var out = '<div class="error">' + data.errors + '</div>';
          form.parent().find('.setting-header').after(out);
        } else {
          $('#friends-list').prepend(profile);
          btn.off('click');
          btn.removeClass('add-friend-btn');
          btn.addClass('delete-friend-btn');
          deleteFriendHandler();
        }
      },
      error: function(msg) {
        // there was a problem
        alert('There was a problem: ' + msg.status + ' ' + msg.statusText);
      }
    });
  });
}

function deleteFriendHandler() {
  $('.delete-friend-btn').click(function(e) {
    $('.error').remove();
    $('.msg').remove();

    var btn = $(this);
    var profile = $(this).parent();
    var formData = new FormData();
    formData.append('delete-friend-btn', '');
    formData.append('friend-id', btn.parent().find('.hidden-id').text());

    $.ajax({
      type: 'POST',
      url: './friends_db.php',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      dataType: 'JSON',
      success: function(data) {
        if(data.errors) {
          var out = '<div class="error">' + data.errors + '</div>';
          form.parent().find('.setting-header').after(out);
        } else {
          profile.remove();
        }
      },
      error: function(msg) {
        // there was a problem
        alert('There was a problem: ' + msg.status + ' ' + msg.statusText);
      }
    });
  });
}

function settingsFormSubmit() {
  $('.setting-form').submit(function(e) {
    e.preventDefault();

    var form = $(this);
    var url = form.attr('action');

    $('.error').remove();
    $('.msg').remove();

    if(e.target == document.getElementById('profile-pic-form')) {
      var img = $('#profile-pic').prop('files')[0];

      var formData = new FormData();
      formData.append('profile-pic', img);
      formData.append(form.find('button').attr('name'), '');

      $.ajax({
        type: 'POST',
        url: url,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'JSON',
        success: function(data) {
          if(data.errors) {
            var out = '<div class="error">' + data.errors + '</div>';
            form.parent().find('.setting-header').after(out);
          } else {
            if(data.update_id && data.update_val) {
              document.getElementById(data.update_id).src = data.update_val;
            }
            var out = '<div class="msg">' + data.msg + '</div>'
            form.parent().find('.setting-header').after(out);
          }
        },
        error: function(msg) {
          // there was a problem
          alert('There was a problem: ' + msg.status + ' ' + msg.statusText);
        }
      });
    } else {
      var formData = form.serialize() + '&' + form.find('button').attr('name') + '=';

      $.ajax({
        type: 'POST',
        url: url,
        data: formData,
        dataType: 'JSON',
        success: function(data) {
          if(data.errors) {
            var out = '<div class="error">' + data.errors + '</div>';
            form.parent().find('.setting-header').after(out);
          } else {
            if(data.update_id && data.update_val) {
              document.getElementById(data.update_id).innerHTML = data.update_val;
            }
            var out = '<div class="msg">' + data.msg + '</div>'
            form.parent().find('.setting-header').after(out);
          }
        },
        error: function(msg) {
          // there was a problem
          alert('There was a problem: ' + msg.status + ' ' + msg.statusText);
        }
      });
    }
  });
}